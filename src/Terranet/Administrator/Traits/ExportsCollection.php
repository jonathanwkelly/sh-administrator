<?php

namespace Terranet\Administrator\Traits;

use DOMDocument;
use Illuminate\Database\Eloquent\Builder;
use Response;
use Terranet\Administrator\Exception;

trait ExportsCollection
{
    protected $exportColumns = ['*'];

    /**
     * Export collection to a specific format
     *
     * @param Builder $query
     * @param         $format
     * @return mixed
     * @throws Exception
     */
    public function export(Builder $query, $format)
    {
        $method = "to" . strtoupper($format);

        if (! method_exists($this, $method)) {
            throw new Exception(sprintf('Don\'t know how to export to %s format', $format));
        }

        return call_user_func_array([$this, $method], [$query]);
    }

    /**
     * Convert & download collection in JSON format
     *
     * @param $query
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function toJSON(Builder $query)
    {
        return response()->json($query->select($this->exportColumns)->get(), 200);
    }

    /**
     * Convert & download collection in XML format
     *
     * @param $query
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function toXML(Builder $query)
    {
        $dom = new DOMDocument();
        $root = $dom->createElement('root');

        $query->select($this->exportColumns)->chunk(100, function ($collection) use ($dom, $root) {
            foreach ($collection as $object) {
                $item = $dom->createElement('item');

                foreach ($object->toArray() as $column => $value) {
                    $column = $dom->createElement($column, $value);
                    $item->appendChild($column);
                }

                $root->appendChild($item);
            }
        });
        $dom->appendChild($root);

        return response($dom->saveXML(), 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * Convert & download collection in CSV format
     *
     * @param $query
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function toCSV(Builder $query)
    {
        return Response::stream(function () use ($query) {
            $out = fopen('php://output', 'w');

            $query->select($this->exportColumns)->chunk(100, function ($collection) use ($out) {
                foreach ($collection as $item) {
                    fputcsv($out, $item->toArray());
                }
            });

            fclose($out);
        });
    }
}

<?php
namespace Cyberduck\LaravelExcel\Exporter;

use Illuminate\Support\Collection;
use Box\Spout\Writer\WriterFactory;
use Illuminate\Database\Query\Builder;
use Cyberduck\LaravelExcel\Serialiser\BasicSerialiser;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;
use Cyberduck\LaravelExcel\Contract\ExporterInterface;

abstract class AbstractSpreadsheet implements ExporterInterface
{
    protected $data;
    protected $type;
    protected $serialiser;
    protected $chuncksize;

    public function __construct()
    {
        $this->data = [];
        $this->type = $this->getType();
        $this->serialiser = new BasicSerialiser();
    }

    public function load(Collection $data)
    {
        $this->data = $data;
        return $this;
    }

    public function loadQuery(Builder $query)
    {
        $this->data = $query;
        return $this;
    }

    public function setChunk($size)
    {
        $this->chunksize = $size;
        return $this;
    }

    public function setSerialiser(SerialiserInterface $serialiser)
    {
        $this->serialiser = $serialiser;
        return $this;
    }

    abstract public function getType();

    public function save($filename)
    {
        $writer = $this->create();
        $writer->openToFile($filename);
        $writer = $this->makeRows($writer);
        $writer->close();
    }

    public function stream($filename)
    {
        $writer = $this->create();
        $writer->openToBrowser($filename);
        $writer = $this->makeRows($writer);
        $writer->close();
    }

    protected function create()
    {
        return WriterFactory::create($this->type);
    }

    protected function makeRows($writer)
    {
        $headerRow = $this->serialiser->getHeaderRow();
        if (!empty($headerRow)) {
            $writer->addRow($headerRow);
        }
        if ($this->data instanceof Builder) {
            if (isset($this->chuncksize)) {
                $this->data->chunk($this->chuncksize);
            } else {
                $data = $this->data->get();
                foreach ($data as $record) {
                    $writer->addRow($this->serialiser->getData($record));
                }
            }
        } else {
            foreach ($this->data as $record) {
                $writer->addRow($this->serialiser->getData($record));
            }
        }
        return $writer;
    }
}

<?php
namespace Cyberduck\LaravelExcel\Contract;

interface ImporterInterface
{
    public function load($path);
    public function setParser(ParserInterface $parser);
    public function getCollection();
    public function setSheet($sheet);
}

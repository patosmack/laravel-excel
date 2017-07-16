<?php
namespace Cyberduck\LaravelExcel\Parser;

use Cyberduck\LaravelExcel\Contract\ParserInterface;

class BasicParser implements ParserInterface
{
    public function transform($row)
    {
        return $row;
    }
}

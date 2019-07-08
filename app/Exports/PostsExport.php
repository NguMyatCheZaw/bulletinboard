<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PostsExport implements FromArray, WithStrictNullComparison, WithHeadings
{
    protected $posts;

    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    function array(): array
    {
        return $this->posts;
    }

    public function headings(): array
    {
        return ["Post Title", "Description", "Status", "Posted User", "Posted Date", "Updated Date"];
    }

}

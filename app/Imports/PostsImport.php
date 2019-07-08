<?php

namespace App\Imports;

use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PostsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $rows
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $rows->transform(function ($row) {
            $this->validationFields($row);
            return [
                'title' => $row['post_title'],
                'description' => $row['description'],
                'status' => $row['status'] ?? 1,
            ];
        });

        foreach ($rows as $each) {
            Post::create([
                'title' => $each['title'],
                'description' => $each['description'],
                'status' => $each['status'] ?? 1,
                'create_user_id' => Auth::id(),
                'updated_user_id' => Auth::id(),
            ]);
        }
/*         $post = new Post([
'title' => $row['post_title'],
'description' => $row['description'],
'status' => $row['status'] ?? 1,
'create_user_id' => Auth::id(),
'updated_user_id' => Auth::id(),
]); */
    }

    public function validationFields($data)
    {
        $customMessages = [
            'required' => 'The :attribute is required.',
            'post_title.unique' => 'The post title is already exist.',
        ];

        Validator::make($data->toArray(), [
            'post_title' => 'required|string|max:255|unique:posts,title',
            'description' => 'required|string|max:255',
            'status' => 'nullable|int',
        ], $customMessages)->validate();
    }
}

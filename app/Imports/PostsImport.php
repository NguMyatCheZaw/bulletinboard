<?php

namespace App\Imports;

use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

class PostsImport implements ToCollection
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
                'title' => $row[0],
                'description' => $row[1],
                'status' => $row[2] ?? 1,
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
    }

    public function validationFields($data)
    {
        $customMessages = [
            'required' => 'The :attribute is required.',
            '0.unique' => 'The post title is already exist.',
        ];

        Validator::make($data->toArray(), [
            '0' => 'required|string|max:255|unique:posts,title',
            '1' => 'required|string|max:255',
            '2' => 'nullable|int',
        ], $customMessages)->validate();
    }
}

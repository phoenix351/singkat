<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;


class UserImport implements ToModel, ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private $currentRow = 0;

    public function collection(Collection $collection) {}
    public function model(array $row)
    {

        $this->currentRow++;

        if ($this->currentRow > 1 && $this->currentRow < 373) {

            // dd($row);

            return new User([
                'name'          => $row[3],
                'username'           => $row[1],
                'pegawai_id'           => $row[1],
                'role' => 'viewer',
                'email' => $row[1] . '@monitoringbps.com',
                'password' => bcrypt('password'),
            ]);
        } else {
            return null;
        }
    }
}

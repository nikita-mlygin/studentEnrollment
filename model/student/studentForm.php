<?php

namespace App\Model;

require_once modelDir . '/base/IModel.php';

use App\Base\Model\IModel;
use App\Entities\Student;

class studentFormModel implements IModel
{
	function __construct() 
    {
	}

    public function addStudent(Student& $student)
    {
        
    }

    public function addStudents(array& $studentsArray)
    {
        foreach ($studentsArray as $item) {
            if($item instanceof Student)
                $this->addStudent($item);
            else
                throw new \Error('Параметр должен быть типом Student');
        }
    }
}

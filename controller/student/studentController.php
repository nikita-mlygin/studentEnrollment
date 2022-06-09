<?php

namespace App\Controller;

require_once controllerDir . '/base/BaseController.php';
require_once entitiesDir . '/Student.php';
require_once viewDir . '/student/studentView.php';

use App\Model\studentFormModel;
use App\Base\Controller\BaseController;
use App\View\StudentView;
use App\Entities\Student;
use App\App;
use App\Base\Sql\BaseSelectQuery;
use App\Base\Sql\BaseInsertQuery;


class StudentController extends BaseController
{
    function runDefault()
    {
    // TODO
    }

    function runAction(string $actionName)
    {
        switch ($actionName) {
            case 'add': {
                    $this->includeModelFile('student');

                    if (\getallheaders()['Content-Type'] == 'application/json') {
                        $data = \json_decode(file_get_contents('php://input'));
                        $student = [];

                        if (isset($data->student)) {
                            array_push($student, Student::create()->getObjByArray($data->student));
                        }
                        else if (isset($data->students)) {
                            foreach ($data->students as $item) {
                                array_push($student, Student::create()->getObjByArray($item));
                            }
                        }


                        echo \json_encode([
                            'student' => $student
                        ]);

                    }
                    else {
                        throw new \Error('Must be AJAX JSON');
                    }

                    $studentModel = new studentFormModel();

                    break;
                }
            case 'viewForm': {
                    $this->viewForm();

                    break;
                }
            case 'getStudents': {
                    $this->includeModelFile('student');

                    break;
                }


            default:
                # code...
                break;
        }
    }

    function viewForm(): bool
    {
        $view = new StudentView();

        $view->viewAddForm();

        return true;
    }



    function __construct()
    {
        parent::__construct(['student' => 'student/studentForm.php']);
    }
}

return new StudentController();
<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace Orangehrm\Rest\Api\Pim;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class EmployeeDetailAPI extends EndPoint
{

    const PARAMETER_ID = "id";

    /*
     * relations
     */
    const EMPLOYEE_CONTACT_DETAIL = "/employee/:id/contact-detail";
    const EMPLOYEE_JOB_DETAIL = "/employee/:id/job-detail";
    const EMPLOYEE_DEPENDENT = "/employee/:id/dependent";

    const PARAMETER_FIRST_NAME = "firstName";
    const PARAMETER_LAST_NAME = "lastName";
    const PARAMETER_MIDDLE_NAME = "middleName";
    const PARAMETER_NUMBER = "number";
    const PARAMETER_DOB = "dob";
    const PARAMETER_GENDER = "gender";
    const PARAMETER_MARITAL_STATUS = 'maritalStatus';
    const PARAMETER_OTHER_ID = 'otherId';
    const PARAMETER_NATIONALITY = 'nationality';
    const PARAMETER_DRIVERS_LICENSE_NUMBER = 'licenseNumber';
    const PARAMETER_DRIVERS_LICENSE_EXP_DATE = 'licenseNumberExpDate';




    /**
     * @return \EmployeeService|null
     */
    protected function getEmployeeService()
    {

        if ($this->employeeService != null) {
            return $this->employeeService;
        } else {
            return new \EmployeeService();
        }
    }

    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Get employee details
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getEmployeeDetails()
    {

        $empId = -1;
        $employeeList [] = array();
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        }
        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");

        }
        $employee = $this->getEmployeeService()->getEmployee($empId);


        if (empty($employee)) {
            throw new RecordNotFoundException("Employee not found");
        }
        return new Response($this->buildEmployeeData($employee), array(
            'contact-detail' => self::EMPLOYEE_CONTACT_DETAIL,
            'job-detail' => self::EMPLOYEE_JOB_DETAIL,
            'dependent' => self::EMPLOYEE_DEPENDENT
        ));

    }

    /**
     * Update employee details
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function updateEmployee(){

        $filters = $this->filterParameters();
        if($this->validateInputs($filters)){
            $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

            $employee = $this->getEmployeeService()->getEmployee($empId);

            if(!empty($employee)){

                if(!empty($filters[self::PARAMETER_FIRST_NAME])){
                    $employee->setFirstName( $filters[self::PARAMETER_FIRST_NAME]);
                }
                if(!empty($filters[self::PARAMETER_MIDDLE_NAME])){
                    $employee->setMiddleName( $filters[self::PARAMETER_MIDDLE_NAME]);
                }
                if(!empty($filters[self::PARAMETER_LAST_NAME])){
                    $employee->setLastName($filters[self::PARAMETER_LAST_NAME]);
                }
                if(!empty($filters[self::PARAMETER_NUMBER])){
                    $employee->setEmployeeId($filters[self::PARAMETER_NUMBER]);
                }
                if(!empty($filters[self::PARAMETER_DOB])){
                   $dob = date('Y-m-d', strtotime($filters[self::PARAMETER_DOB]));
                    $employee->setEmpBirthday( $filters[self::PARAMETER_DOB] );
                }
                if(!empty($filters[self::PARAMETER_GENDER])){
                    if($filters[self::PARAMETER_GENDER] == 'M'){
                        $employee->setEmpGender(1);
                    }else if ($filters[self::PARAMETER_GENDER] == 'F'){
                        $employee->setEmpGender(2);
                    }

                }
                if(!empty($filters[self::PARAMETER_OTHER_ID])){
                    $employee->setOtherId($filters[self::PARAMETER_OTHER_ID]);
                }
                if(!empty($filters[self::PARAMETER_DRIVERS_LICENSE_NUMBER])){
                    $employee->setEmpDriLiceExpDate($filters[self::PARAMETER_DRIVERS_LICENSE_EXP_DATE]);
                }
                if(!empty($filters[self::PARAMETER_DRIVERS_LICENSE_NUMBER])){
                    $employee->setLicenseNo($filters[self::PARAMETER_DRIVERS_LICENSE_NUMBER]);
                }

                $returnedEmp = $this->getEmployeeService()->saveEmployee($employee);
                if($returnedEmp instanceof \Employee){
                    return new Response(array('success' => 'successfully updated'));
                }else {
                    throw new BadRequestException("updating failed");
                }
            }else {
                throw new BadRequestException("employee not found");
            }



        }else {
            throw new InvalidParamException("updating failed");
        }

    }

    /**
     * Get post parameters
     *
     * @return array
     */
    protected function filterParameters()
    {

        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_FIRST_NAME))) {
            $filters[self::PARAMETER_FIRST_NAME] = ($this->getRequestParams()->getPostParam(self::PARAMETER_FIRST_NAME));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_MIDDLE_NAME))) {
            $filters[self::PARAMETER_MIDDLE_NAME] = ($this->getRequestParams()->getPostParam(self::PARAMETER_MIDDLE_NAME));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_LAST_NAME))) {
            $filters[self::PARAMETER_LAST_NAME] = ($this->getRequestParams()->getPostParam(self::PARAMETER_LAST_NAME));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NUMBER))) {
            $filters[self::PARAMETER_NUMBER] = ($this->getRequestParams()->getPostParam(self::PARAMETER_NUMBER));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_STATUS))) {
            $filters[self::PARAMETER_STATUS] = ($this->getRequestParams()->getPostParam(self::PARAMETER_STATUS));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DOB))) {
            $filters[self::PARAMETER_DOB] = ($this->getRequestParams()->getPostParam(self::PARAMETER_DOB));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_GENDER))) {
            $filters[self::PARAMETER_GENDER] = ($this->getRequestParams()->getPostParam(self::PARAMETER_GENDER));
        }
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $filters[self::PARAMETER_ID] = ($this->getRequestParams()->getPostParam(self::PARAMETER_ID));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NATIONALITY))) {
            $filters[self::PARAMETER_NATIONALITY] = ($this->getRequestParams()->getPostParam(self::PARAMETER_NATIONALITY));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_MARITAL_STATUS))) {
            $filters[self::PARAMETER_GENDER] = ($this->getRequestParams()->getPostParam(self::PARAMETER_GENDER));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_GENDER))) {
            $filters[self::PARAMETER_MARITAL_STATUS] = ($this->getRequestParams()->getPostParam(self::PARAMETER_MARITAL_STATUS));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DRIVERS_LICENSE_NUMBER))) {
            $filters[self::PARAMETER_DRIVERS_LICENSE_NUMBER] = ($this->getRequestParams()->getPostParam(self::PARAMETER_DRIVERS_LICENSE_NUMBER));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DRIVERS_LICENSE_EXP_DATE))) {
            $filters[self::PARAMETER_DRIVERS_LICENSE_EXP_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_DRIVERS_LICENSE_EXP_DATE));
        }
        return $filters;

    }

    /**
     * validate input parameters
     *
     * @param $filters
     * @return bool
     */
    protected function validateInputs($filters)
    {
        $valid = true;

        $format = "Y-m-d";

        if (!(preg_match("/^[a-zA-Z'-]+$/", $filters[self::PARAMETER_FIRST_NAME]) === 1) || (strlen($filters[self::PARAMETER_FIRST_NAME]) > 30 )) {
            return  false;

        }
        if (!empty($filters[self::PARAMETER_MIDDLE_NAME]) &&!(preg_match("/^[a-zA-Z'-]+$/", $filters[self::PARAMETER_MIDDLE_NAME]) === 1) || (strlen($filters[self::PARAMETER_MIDDLE_NAME]) > 30 )) {
            return  false;

        }
        if (!(preg_match("/^[a-zA-Z'-]+$/", $filters[self::PARAMETER_LAST_NAME]) === 1) || (strlen($filters[self::PARAMETER_LAST_NAME]) > 30 )) {
            return false;

        }

        if (!empty($filters[self::PARAMETER_DOB]) && !$this->validateDate($filters[self::PARAMETER_DOB])
        ) {
           return false;
        }

        if (!empty($filters[self::PARAMETER_DRIVERS_LICENSE_EXP_DATE]) && !$this->validateDate($filters[self::PARAMETER_DRIVERS_LICENSE_EXP_DATE])
        ) {
            return false;
        }

        if (!empty($filters[self::PARAMETER_GENDER]) && !( $filters[self::PARAMETER_GENDER]=='M'  || $filters[self::PARAMETER_GENDER] =='F' )) {
             return false;
        }

        return $valid;
    }
    /**
     * Creating the Employee serializable object
     *
     * @param $employee
     * @return array
     */
    private function buildEmployeeData(\Employee $employee)
    {

        $emp = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(),
            $employee->getEmployeeId());
        $emp->buildEmployee($employee);
        return $emp->toArray();

    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
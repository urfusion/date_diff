<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class CalcController extends AppController {
    
    /*
     * @access public
     * Ajax based param start_date, end_date
     * @return difference b/w two dates
     */

    public function index() {

        if ($this->request->is(array('ajax'))) {
            $start_date = $this->request->getData('start_date');
            $end_date = $this->request->getData('end_date');

            $date1 = strtotime($start_date);
            $date2 = strtotime($end_date);

            $diff = abs($date2 - $date1);

            $years = floor($diff / (365 * 60 * 60 * 24));

            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

            $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));

            $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
            $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

            $resultJ = array(
                'years' => $years,
                'months' => $months,
                'days' => $days,
                'hours' => $hours,
                'minutes' => $minutes,
                'seconds' => $seconds,
                'diff' => $diff,
                'diff_in_minuts' => ($diff / 60),
                'diff_in_hours' => ($diff / (60 * 60)),
                'diff_in_days' => ($diff / (60 * 60 * 24)),
                'diff_in_weeks' => ($diff / (60 * 60 * 24 * 7)),
                'diff_in_months' => ($diff / (60 * 60 * 24 * 30)),
                'diff_in_years' => ($diff / (60 * 60 * 24 * 30 * 12)),
            );

            $logsTable = TableRegistry::get('Logs');
            $log = $logsTable->newEntity();
            $log->start_date = $start_date;
            $log->end_date = $end_date;
            $log->date_diff = $diff;
            
            if ($logsTable->save($log)) {
                $resultJ['error'] = 0;
            } else {
                $resultJ['error'] = 1;
            }
            
            $this->response->type('json');
            $this->response->body(json_encode($resultJ));

            return $this->response;
        }

    }

}

<?
IncludeModuleLangFile(__FILE__);


class CAllSeoConnect {
     public $LAST_ERROR;

     function error($error = false) {
          if($error === false) {
               return $this->LAST_ERROR;
          } else {
               $this->LAST_ERROR = $error;
               return false;
          }
     }

}


class CAllSeoConnectPage extends CAllSeoConnect
{
     function add($arFields){

     }

     function getList($order, $filter, $nav, $select) {

     }

     function getById($id) {

     }


     function delete($id) {

     }

     function update($id, $arFields) {


     }

}

class CAllSeoConnectTitle extends CAllSeoConnect
{

     function add($arFields){

     }

     function getList($order, $filter, $nav, $select) {

     }

     function getById($id) {

     }


     function delete($id) {

     }

     function update($id, $arFields) {


     }


}

class CAllSeoConnectAnchor extends CAllSeoConnect
{
     function add($arFields){

     }

     function getList($order, $filter, $nav, $select) {

     }

     function getById($id) {

     }


     function delete($id) {

     }

     function update($id, $arFields) {


     }
}


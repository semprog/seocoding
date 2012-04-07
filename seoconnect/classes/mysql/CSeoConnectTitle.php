<?

class CSeoConnectTitle extends CAllSeoConnectTitle
{
     public static $_keys = array('ID','TITLE','ACTIVE');

     function add($arFields){
        global $DB;
        if(!$arFields['TITLE']) {
             $this->error('NO TITLE!');
             return false;
        }



		$arInsert = $DB->PrepareInsert("b_seoconnect_titles", $arFields);
		$strSql = "INSERT INTO b_seoconnect_titles(".$arInsert[0].") "."VALUES(".$arInsert[1].")";
		$DB->Query($strSql);
		$ID = $DB->LastID();

        return $ID;
     }


    function getList($filter = array(), $order = array('ID' => 'ASC') , $nav = array(), $select = array()) {
    		global $DB;

            $selectSql = array();
            if($select) {
                  foreach($select as $val) {

                       $val = strtoupper($val);

                       if(in_array($val, self::$_keys)) {
                            $selectSql[] = $val;
                       }
                  }
            }
            if($selectSql) {
                 $selectSql = implode(', ', $selectSql);
            } else {
                 $selectSql = "*";
            }
            $where = '';
            if(!empty($filter)) {
                  $where = array();
                  foreach($filter as $key => $val) {
                     if(in_array($key, self::$_keys)) {
                       $where[] = "(`$key` = '$val')";
                     }
                  }
                  if(!empty($where)) {
                      $where = "WHERE " . implode(' AND ', $where);
                  } else {
                      $where = '';
                  }

            }


            $orderSql = array();
            foreach($order as $key => $val) {
                 $key = strtoupper($key);
                 $val = strtoupper($val);
                 $val = ($val == 'DESC') ? 'DESC' : 'ASC';

                 if($key == "RAND") {
                     $orderSql[] = "RAND()";
                     continue;
                 }

                 if(in_array($key, self::$_keys)) {
                      $orderSql[] = "$key $val";
                 }
            }
            if(count($orderSql)) {
                $orderSql  = "ORDER BY " . implode(', ', $orderSql);
            } else {
                $orderSql = "";
            }


            if($nav) {
              	$nTopCount = intval($nav["nTopCount"]);
                if($nTopCount) {
                    $limitSql = "LIMIT " . $nTopCount;
                }

            }

    		$res = $DB->Query("SELECT $selectSql FROM `b_seoconnect_titles` $where $orderSql $limitSql", false, $err_mess.__LINE__);

    		return $res;
     }


     function getById($id) {
    		global $DB;

            if(!$id = intval($id)) return false;

    		$res = $DB->Query("SELECT * FROM `b_seoconnect_titles` WHERE `ID` = $id", false, $err_mess.__LINE__);

    		return $res;
     }

     function getRandom() {
         return self::getList(array('ACTIVE' => 'Y'), array("RAND" => 'ASC'), array('nTopCount' => 1), array('ID'));
     }


     function delete($id) {
		global $DB;
        if(!$id = intval($id)) return false;

		$strSql ="DELETE FROM `b_seoconnect_titles` WHERE `ID`=".$id;
		$DB->Query($strSql);

        $strSql = "UPDATE `b_seoconnect_pages` SET `TITLE` = NULL WHERE `TITLE`=" . $id;
        $DB->Query($strSql);
		return true;
     }

     function update($id, $arFields) {
           global $DB;
           if(!$id = intval($id)) {
              $this->error('WRONG ID!');
              return false;
           }
           $strUpdate = $DB->PrepareUpdate("b_seoconnect_titles", $arFields);
           $strSql = "UPDATE b_seoconnect_titles SET ".$strUpdate." WHERE ID=".$id;

           $DB->Query($strSql);
           return true;
     }


}
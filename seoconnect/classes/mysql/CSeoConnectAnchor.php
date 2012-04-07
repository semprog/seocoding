<?


class CSeoConnectAnchor  extends CAllSeoConnectAnchor
{
     public static $_keys = array('ID','ANCHOR','URL','WEIGHT','ACTIVE');
     public static $_mysqlSummWeightInited = false;
     public static $_sum_weight = 0;

     function add($arFields){
        global $DB;
        if(!$arFields['URL']) {
             $this->error('NO URL!');
             return false;
        }
        if(!$arFields['ANCHOR']) {
             $this->error('NO ANCHOR!');
             return false;
        }



		$arInsert = $DB->PrepareInsert("b_seoconnect_anchors", $arFields);
		$strSql = "INSERT INTO b_seoconnect_anchors(".$arInsert[0].") "."VALUES(".$arInsert[1].")";
		$DB->Query($strSql);
		$ID = $DB->LastID();

        return $ID;
     }

    function getList($filter = array(), $order = array('ID' => 'ASC') , $nav = array(), $select = array()) {
    		global $DB;

            $selectSql = array();
            $PROBABILITY = '';
            if(in_array("PROBABILITY", $select)) {
                $key = array_search("PROBABILITY", $select);

                if(!self::$_mysqlSummWeightInited) {
                   $sql = "SELECT @sum_weight := SUM(WEIGHT) as sum_weight   FROM `b_seoconnect_anchors`;";
                   $res = $DB->Query($sql)->Fetch();

                   self::$_sum_weight  = $res['sum_weight'];
                   $PROBABILITY = "(WEIGHT / @sum_weight) as PROBABILITY";

                   self::$_mysqlSummWeightInited = true;
                }

                unset($select[$key]);
            }


            if(!empty($select)) {
                  foreach($select as $val) {

                       $val = strtoupper($val);

                       if(in_array($val, self::$_keys)) {
                            $selectSql[] = $val;
                       }
                  }
            }
            if(!$selectSql) {
               $selectSql = array("*");
            }

            if($PROBABILITY) {
                $selectSql[] = $PROBABILITY;
            }


            $selectSql = implode(', ', $selectSql);
            $where = '';
            if(!empty($filter)) {
                  $where = array();
                  foreach($filter as $key => $val) {
                     $sign = "=";
                     if($key[0] == '!') {
                          $key = substr($key,1);
                          $sign = "!=";
                     }

                     $val = $DB->ForSql($val);

                     if(in_array($key, self::$_keys)) {
                       $where[] = "(`$key` ".$sign." '$val')";
                     }
                  }
                  if(!empty($where)) {
                      $where = "WHERE " . implode(' AND ', $where);
                  }

            }


            $orderSql = array();
            if(is_array($order) && !empty($order))
            {
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
            }
            if(count($orderSql)) {
                $orderSql  = "ORDER BY " . implode(', ', $orderSql);
            } else {
                $orderSql  = "ORDER BY `ID` ASC";
            }


            if($nav) {
              	$nTopCount = intval($nav["nTopCount"]);
                if($nTopCount) {
                    $limitSql = "LIMIT " . $nTopCount;
                }

            }

    		$res = $DB->Query("SELECT $selectSql FROM `b_seoconnect_anchors` $where $orderSql $limitSql", false, $err_mess.__LINE__);

    		return $res;
     }

     function getById($id) {
    		global $DB;

            if(!$id = intval($id)) return false;

    		$res = $DB->Query("SELECT * FROM `b_seoconnect_anchors` WHERE `ID` = $id", false, $err_mess.__LINE__);

    		return $res;
     }


     function delete($id) {
		global $DB;
        if(!$id = intval($id)) return false;

		$strSql ="DELETE FROM `b_seoconnect_anchors` WHERE `ID`=".$id;
		$DB->Query($strSql);

        $strSql = "DELETE FROM `b_seoconnect_pages_anchors` WHERE `ANCHOR`=" . $id;
        $DB->Query($strSql);
		return true;
     }

     function update($id, $arFields) {
           global $DB;
           if(!$id = intval($id)) {
              $this->error('WRONG ID!');
              return false;
           }
           $strUpdate = $DB->PrepareUpdate("b_seoconnect_anchors", $arFields);
           $strSql = "UPDATE `b_seoconnect_anchors` SET ".$strUpdate." WHERE ID=".$id;

           $DB->Query($strSql);
           return true;
     }


     function getRandom($count = 1, $presentIds, $stop_url = false) {

            $filter = array('ACTIVE' => 'Y');
            if($stop_url !== false) {
                $filter['!URL'] = $stop_url;
            }
            $rsAnchors = self::getList($filter, array('RAND' => 'ASC'), false);

            $arChoise = array();


            $arAnchors = array();
            $selectedAnchors = array();

            while($anchor = $rsAnchors->Fetch()) {
                if(in_array($anchor['ID'], $presentIds)) continue; 
                $arAnchors[] = $anchor;

            }

            do {
                $arSumWeight = 0;
                if(empty($arAnchors)) break;
                foreach($arAnchors as $anchor) {
                   $arSumWeight += intval($anchor['WEIGHT']);
                }
                $intStep = 0;
                $rndWeight = $arSumWeight * (mt_rand()/mt_getrandmax());

                foreach($arAnchors as $key => $anchor) {
				    if($rndWeight>=$intStep && $rndWeight<=$intStep+$anchor["WEIGHT"]) {
                        $selectedAnchors[$anchor['ID']] = $anchor;
                        unset($arAnchors[$key]);
                        break;
                    }
					$intStep += $anchor["WEIGHT"];
                }


            } while(count($selectedAnchors) < $count);




            ksort($selectedAnchors);
            return $selectedAnchors;
     }

}
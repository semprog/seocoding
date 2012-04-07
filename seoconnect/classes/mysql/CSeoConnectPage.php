<?



class CSeoConnectPage extends CAllSeoConnectPage
{
     public static $_keys = array('ID','PAGE','TITLE');


     function add($arFields){
        global $DB;
        if(!$arFields['PAGE']) {
             $this->error('NO PAGE!');
             return false;
        }

		$arInsert = $DB->PrepareInsert("b_seoconnect_pages", $arFields);
		$strSql =
		"INSERT INTO b_seoconnect_pages(".$arInsert[0].") "."VALUES(".$arInsert[1].")";
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
            }


            if($nav) {
              	$nTopCount = intval($nav["nTopCount"]);
              	$nStart = intval($nav["start"]);

                if($nStart) {
                     $nStart = "$nStart,";
                } else {
                     $nStart = '';
                }

                if($nTopCount) {
                    $limitSql = "LIMIT $nStart $nTopCount";
                }

            }

    		$res = $DB->Query("SELECT $selectSql FROM `b_seoconnect_pages` $where $orderSql $limitSql", false, $err_mess.__LINE__);

    		return $res;
     }

     function getById($id) {
    		global $DB;

            if(!$id = intval($id)) return false;

    		$res = $DB->Query("SELECT * FROM `b_seoconnect_pages` WHERE `ID` = $id", false, $err_mess.__LINE__);

    		return $res;
     }

     function getByUrl($url) {
    		global $DB;

            if(!$url) return false;

            $url = mysql_escape_string($url);

    		$res = $DB->Query("SELECT * FROM `b_seoconnect_pages` WHERE `PAGE` = '$url'", false, $err_mess.__LINE__);

    		return $res;
     }

     function delete($id) {
		global $DB;
        if(!$id = intval($id)) return false;

		$strSql ="DELETE FROM `b_seoconnect_pages` WHERE `ID`=".$id;
		$DB->Query($strSql);

        self::removeAnchors($id);

		return true;
     }

     function update($id, $arFields) {
           global $DB;
           if(!$id = intval($id)) {
              $this->error('WRONG ID!');
              return false;
           }
           $strUpdate = $DB->PrepareUpdate("b_seoconnect_pages", $arFields);
           $strSql = "UPDATE b_seoconnect_pages SET ".$strUpdate." WHERE ID=".$id;

           $DB->Query($strSql);
           return true;
     }

     function addAnchor($pageId, $anchorId, $check = false) {
    	  global $DB;
          if(!$pageId = intval($pageId)) {
              $this->error('WRONG PAGE ID!');
              return false;
           }
           if(!$anchorId = intval($anchorId)) {
              $this->error('WRONG ANCHOR ID!');
              return false;
           }
           if($check) {
             if(!CSeoConnectAnchor::getById($anchorId)->Fetch()) return false;
             if(!CSeoConnectPage::getById($pageId)->Fetch()) return false;
           }

           $arFields = array(
             "PAGE" => $pageId,
             "ANCHOR" => $anchorId
           );

           $arInsert = $DB->PrepareInsert("b_seoconnect_pages_anchors", $arFields);
    	   $strSql =
    	   "INSERT INTO b_seoconnect_pages_anchors(".$arInsert[0].") "."VALUES(".$arInsert[1].")";
    	   $DB->Query($strSql);
           return true;
     }

     function setAnchors($pageId, $ids, $check = false) {
           if(!$pageId = intval($pageId)) {
              $this->error('WRONG PAGE ID!');
              return false;
           }
           if(empty($ids) || !is_array($ids)) return false;
           $this->removeAnchors($pageId);
           foreach($ids as $id) {
               $this->addAnchor($pageId, $id, $check);
           }
     }

     function getAnchors($pageId) {
    		global $DB;

            if(!$pageId = intval($pageId)) return new CDBResult;

    		$res = $DB->Query("SELECT sa.* FROM `b_seoconnect_anchors` sa JOIN `b_seoconnect_pages_anchors` spa ON sa.ID = spa.ANCHOR  WHERE sa.`ACTIVE` = 'Y' AND `PAGE` = $pageId", false, $err_mess.__LINE__);

    		return $res;

     }

     function removeAnchors($pageId, $ids = false) {
    	   global $DB;
           if(!$pageId = intval($pageId)) return false;
           if($ids === false) {
                $strSql = "DELETE FROM `b_seoconnect_pages_anchors` WHERE `PAGE`=" . $pageId;
                $DB->Query($strSql);
           } else if(!empty($ids)) {
                foreach($ids as $id) {
                    $strSql = "DELETE FROM `b_seoconnect_pages_anchors` WHERE `PAGE`=$pageId AND `ANCHOR` =$id LIMIT 1";
                    $DB->Query($strSql);
                }
           }
           return true;
     }


     function reIndex($STEP = 0) {
         $START_EXEC_TIME = microtime(true);
         $STEP_TIME = 15;


         $rsPages = self::getList(array(), array('ID'=>'ASC'), array('nTopCount' => 1000,'start' => $STEP));
         $curStep = 0;
         while($tmp = $rsPages->Fetch()) {
             self::processPage($tmp,true);

             $STEP++;
             $curStep++;
             if(!(microtime(true) - $START_EXEC_TIME < $STEP_TIME)) return $STEP;
         }

         if($curStep < 1000) return false;
         return $STEP;
     }

     function processUrl($url, $rewind = false) {

          $robotsTxt = new RobotsTxt("http://".$_SERVER["SERVER_NAME"]);
          if(!$robotsTxt->allow($url)) return false;



          //echo $MAX_ANCHORS;
          $page = self::getByUrl($url)->Fetch();
          if(!$page) {

             $title = CSeoConnectTitle::getRandom()->Fetch();

             $pageId = $this->add(array(
               "PAGE" => $url,
               "TITLE" =>  $title['ID']
             ));

             $page = self::getById($pageId)->Fetch();
          }

          if(!$page['TITLE']) {
                $title = CSeoConnectTitle::getRandom()->Fetch();
                $this->update($page['ID'], array('TITLE' => $title['ID']));
                $page['TITLE']  =  $title;
          }

          if(!is_array($page['TITLE'])) {
              $title = CSeoConnectTitle::getById($page['TITLE'])->Fetch();
              $page['TITLE'] = $title;
          }

          return self::processPage($page, $rewind) ;

     }

     function processPage($page, $rewind = false) {


          $MAX_ANCHORS = COption::GetOptionString('seoconnect','ANCHORS_CNT');


          $anchors = array();
          if(!$rewind) {
              $rsAnchors = self::getAnchors($page['ID']);
              while($tmp = $rsAnchors->Fetch()){
                 $anchors[$tmp['ID']] = $tmp;
              }

          }

          $anchorsCnt = count($anchors);
          if($anchorsCnt > $MAX_ANCHORS) {
              $presentIds = array_keys($presentAnchors);
              $leftIds = array();
              for($i=0;$i<$MAX_ANCHORS;$i++) {
                 $leftIds[] = $presentIds[$i];
              }
               self::setAnchors($page['ID'],$leftIds);
               $anchorsCnt = $MAX_ANCHORS;
          }




          if($anchorsCnt < $MAX_ANCHORS) {
               $presentAnchors = $anchors;
               $presentIds = array_keys($presentAnchors);



               $anchors = CSeoConnectAnchor::getRandom($MAX_ANCHORS - $anchorsCnt,$presentIds ,$page['PAGE']);


               foreach($anchors as $anchor) {
                   $presentIds[] = $anchor['ID'];
                   $presentAnchors[]  = $anchor;
               }

               $anchors =  $presentAnchors;

               self::setAnchors($page['ID'],$presentIds);
          }

          $page['ANCHORS'] = $anchors;



          return $page;

     }


}
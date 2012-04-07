<?php


class RobotsTxt
{

    private $_site = '';

    private $_directives = array();

    /**
     * �����������
     *
     * @param $url string - URL, ��� �������� ������� ������� robots.txt
     */
    public function __construct($url, $debug = false)
    {
        $this->debug = $debug;

        if (false === ($arUrl = @parse_url($url))) {
            throw new Exception('���������� ���������� URL "' . $url . '"');
        }
        if (empty($arUrl['scheme']) || empty($arUrl['host'])) {
            $er = '��������� URL "' . $url
                . '" �� �������� ����� � ����� �����';
            throw new Exception($er);
        }
        $this->_site = $arUrl['scheme'] . '://' . $arUrl['host'] . '/';
        $url = $this->_site . 'robots.txt';
        if (false === ($directives = @file($url))) {
            $er = '���� ' . $url . ' �� ���������� ��� �� ����� ���� ��������.';
            throw new Exception($er);
        }


       foreach($directives as $str) {
          preg_match('/Disallow: (.*)/is', $str, $matches);
          if($matches[1]) {
             $this->_directives[]=trim($matches[1]);
          }
       }




        if($debug) {
           // echo "<pre>";
           // var_dump($this->_directives);
        }
    }

    /**
     * ���������� ����� �����, � ���� robots.txt ��������
     *
     * @return string - ����� �����
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * ���������� ������ �������� �� robots.txt � ����������������� ����
     *
     * @return array - ������ ��������
     */
    public function getDirectives()
    {
        return $this->_directives;
    }

    /**
     * ���������, �������� �� � robots.txt ������ URL ��� ��������� � ����
     * ������� ����
     *
     * @param $url string - ������ URL
     * @return boolean - �������� ��� ��������
     */
    public function allow($url)
    {

         foreach($this->_directives as $item) {
          $item=str_replace(array('.','*','+','/','?'), array('\.','.*','\+','\/','\?'), $item);
          if(preg_match('/^'.$item.'/is',$url)) return false; // $-�������� � ����� ��������� �������������


          //    echo '/^'.$item.'/is', ' ', $url, '<br />';


         }
         return true;
    }
}
<?php


class RobotsTxt
{

    private $_site = '';

    private $_directives = array();

    /**
     * Конструктор
     *
     * @param $url string - URL, для которого следует парсить robots.txt
     */
    public function __construct($url, $debug = false)
    {
        $this->debug = $debug;

        if (false === ($arUrl = @parse_url($url))) {
            throw new Exception('Невозможно распарсить URL "' . $url . '"');
        }
        if (empty($arUrl['scheme']) || empty($arUrl['host'])) {
            $er = 'Введенный URL "' . $url
                . '" не содержит схемы и имени хоста';
            throw new Exception($er);
        }
        $this->_site = $arUrl['scheme'] . '://' . $arUrl['host'] . '/';
        $url = $this->_site . 'robots.txt';
        if (false === ($directives = @file($url))) {
            $er = 'Файл ' . $url . ' не существует или не может быть загружен.';
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
     * Возвращает адрес сайта, с чьим robots.txt работаем
     *
     * @return string - Адрес сайта
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * Возвращает список директив из robots.txt в структурированном виде
     *
     * @return array - Список директив
     */
    public function getDirectives()
    {
        return $this->_directives;
    }

    /**
     * Проверяет, разрешен ли в robots.txt данный URL для обращения к нему
     * данного бота
     *
     * @param $url string - Данный URL
     * @return boolean - Разрешен или запрещен
     */
    public function allow($url)
    {

         foreach($this->_directives as $item) {
          $item=str_replace(array('.','*','+','/','?'), array('\.','.*','\+','\/','\?'), $item);
          if(preg_match('/^'.$item.'/is',$url)) return false; // $-привязка к концу сработает автоматически


          //    echo '/^'.$item.'/is', ' ', $url, '<br />';


         }
         return true;
    }
}
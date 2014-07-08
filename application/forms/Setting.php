<?php

class Application_Form_Setting extends Zend_Form
{
    public function init()
    {
    	
        $this->setName('link');

        $idsetting = new Zend_Form_Element_Hidden('idsetting');
        $idsetting->addFilter('Int');
        
        $siteid = new Zend_Form_Element_Hidden('siteid');
        //$siteid->setLabel('Сайт');
        $siteid->addFilter('Int');
                
        $host = new Zend_Form_Element_Hidden('host');

        $googlerank = new Zend_Form_Element_Text('googlerank');        
        $googlerank->setLabel('До какой позиции продвигать в Google')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');
        	
        $yandexrank = new Zend_Form_Element_Text('yandexrank');        
        $yandexrank->setLabel('До какой позиции продвигать в Яндекс')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int')
        	->addValidator('StringLength', true, array(
                'min' => 2
            ));
        	
        $maxlinksday = new Zend_Form_Element_Text('maxlinksday');        
        $maxlinksday->setLabel('Ограничение числа ссылок в день на страницу')
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');
        	
        $minpageview = new Zend_Form_Element_Text('minpageview');        
        $minpageview->setLabel('Трафик на страницу для продвижения')
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');

        /*
        $minqueryview = new Zend_Form_Element_Text('minqueryview');        
        $minqueryview->setLabel('Трафик на страницу для продвижения')
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');
        */
        	
       	$maxquerylength = new Zend_Form_Element_Text('maxquerylength');
        $maxquerylength->setLabel('Ограничение длинны запроса')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');
        	
        	
        $numberlinks = new Zend_Form_Element_Select('numberlinks');
        $numberlinks->setLabel('Ставить ссылок за раз')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim');

        $numberlinks->addMultiOption('1', '1');
        $numberlinks->addMultiOption('2', '2'); 	
        $numberlinks->addMultiOption('3', '3'); 
        
        
        $querystatus = new Zend_Form_Element_Select('querystatus');        
        $querystatus->setLabel('Модерация новых запросов')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');        	
        
        $querystatus->addMultiOption('WAIT', 'Да');
        $querystatus->addMultiOption('OK', 'Нет');
        //$querystatus->addMultiOption('DEL');
        	
        /*
        $pagestatus = new Zend_Form_Element_Select('pagestatus');        
        $pagestatus->setLabel('Статус новыx страниц')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');        	      	
        
        $pagestatus->addMultiOption('OK', 'OK');
        $pagestatus->addMultiOption('LINK', 'LINK');
        $pagestatus->addMultiOption('PROMO', 'PROMO');
        $pagestatus->addMultiOption('DEL', 'DEL');
        */
        
        $linking = new Zend_Form_Element_Select('linking');        
        $linking->setLabel('Включить перелинковку')        
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        	
        $linking->addMultiOption('1', 'Да');
        $linking->addMultiOption('0', 'Нет');
        	
        $maxquerylinks = new Zend_Form_Element_Text('maxquerylinks');        
        $maxquerylinks->setLabel('Максимум ссылок на продвигаемую страницу')
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');
        	
        $maxpagelinks = new Zend_Form_Element_Text('maxpagelinks');
        $maxpagelinks->setLabel('Число ссылок выводимых на странице сайта')
        	->addFilter('StripTags')
        	->addFilter('StringTrim')
        	->addValidator('int');        	
       	
        /*
        $linklistclass = new Zend_Form_Element_Text('linklistclass');
        $linklistclass->setLabel('Стиль оформления ul ссылок')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        	
        $linkitemclass = new Zend_Form_Element_Text('linkitemclass');
        $linkitemclass->setLabel('Стиль оформления li ссылки')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        	
        $linknofound = new Zend_Form_Element_Text('linknofound');
        $linknofound->setLabel('Выводить заместо ссылок если ничего не найдено')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        */
        	
        $multisite_linking = new Zend_Form_Element_Select('multisite_linking');
        $multisite_linking->setLabel('Разрешить продвижение сайта за счет других сайтов в БД')
        	->addFilter('StripTags')
        	->addFilter('StringTrim');
        	
        $multisite_linking->addMultiOption('1', 'Да');
        $multisite_linking->addMultiOption('0', 'Нет');
        	
        $multisite_maxquerylinks = new Zend_Form_Element_Text('multisite_maxquerylinks');
        $multisite_maxquerylinks->setLabel('Максимальное число ссылок со внешнего сайта на продвигаемую страницу')
        	->addFilter('StripTags')
        	->addFilter('StringTrim')        	
        	->addValidator('int');
        	
        	
        $delete = new Zend_Form_Element_Submit('delete');
        $delete->setAttrib('onclick', 'return confirm("Желаете удалить сайт?") ? true : false;'); 
        $delete->setAttrib('class', 'btn btn-danger');
        $delete->setLabel('Удалить сайт');
        	
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');  
        $submit->setAttrib('class', 'btn btn-primary');
        

        $this->addElements(array($idsetting, $siteid, $host, $googlerank, $yandexrank, $maxlinksday,
        	$minpageview, $maxquerylength, $querystatus, $numberlinks, /*$pagestatus,*/ $linking, $maxquerylinks, $maxpagelinks, 
        	//$linklistclass,	$linkitemclass, $linknofound,
        	$multisite_linking, $multisite_maxquerylinks, $submit, $delete));
        	
        	
        // Группа полей связанных с личной информацией
        $this->addDisplayGroup(
            array('idsetting', 'siteid', 'host', 'googlerank', 'yandexrank', 'maxlinksday',
            	'minpageview', 'maxquerylength', 'numberlinks', 'querystatus'),
            'promotionGroup',
            array(
                'legend' => 'Натройка продвижения сайта'
            )
        );
        
        $this->addDisplayGroup(
            array('linking', /*'pagestatus',*/ 'maxquerylinks', 'maxpagelinks', 'linklistclass',
            	'linkitemclass', 'linknofound'),
            'linkingGroup',
            array(
                'legend' => 'Наcтройка внутренней перелинковки'
            )
        );

        // Группа полей связанных с личной информацией
        $this->addDisplayGroup(
            array('multisite_linking', 'multisite_maxquerylinks'),
            'multisiteLinkingGroup',
            array(
                'legend' => 'Наcтройка внешней перелинковки'
            )
        );
        
        // Группа полей кнопок
        $this->addDisplayGroup(
            array('submit', 'delete'), 'buttonsGroup'
        );
        
    }    
    
	public function setSite($site)
    {
        $select = $this->getElement('host');        
        //print_r($site->host);        
        $select->setValue($site->host);        
    }
    
 
    
}
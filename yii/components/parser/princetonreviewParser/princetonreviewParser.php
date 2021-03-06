<?php
namespace app\components\parser\princetonreviewParser;
use app\components\parser\parser;
use phpQuery;
use Yii;
ini_set('max_execution_time', 100000);


class princetonreviewParser extends parser{
    private $CollegiesID = array();
    private function getPageAmount()
    {
        $doc = $this->grab('https://www.princetonreview.com/college-search?ceid=cp-1022984&');
        $pagesDoc = $doc->find('#Page')->next()->text();
        $pages = explode(" ",$pagesDoc);
        return end($pages);
    }
    public function gelAllCollegies()
    {
        $college_id = 1;
        $pageNumbers = $this->getPageAmount();
        for ($i = 1; $i <= $pageNumbers;$i++)
        {
            $images_src = array(); // Ссылка на картинку
            $college_name = array();// Название Колледжа
            $city = array(); // Город
            $state = array(); // Штат
            $url = ('https://www.princetonreview.com/college-search?ceid=cp-1022984&page='.$i);
            $doc = $this->grab($url);
            $school_locations = $doc->find('h2')->next();
            $school_images = $doc->find("img[class^='school-image'");
            $collegies_names = $doc->find('h2 a');
            $locations = array();
            $img_alt = array();
            foreach ($school_locations as $row)
                if(pq($row)->attr('class') == 'location')
                    array_push($locations,pq($row)->text());
                else
                    array_push($locations,"Without city,Without state");

            foreach ($locations as $row)
                list($city[],$state[]) = explode(',',$row);

            foreach ($collegies_names as $key => $row) {
                array_push($college_name, pq($row)->text());
                if(array_key_exists(pq($row)->text(), $this->CollegiesID))
                    $college_id++;
                else{
                    $this->CollegiesID[pq($row)->text()] = $college_id;
                    $college_id++;
                }
                foreach ($school_images as $img) {
                    if ((pq($img)->attr('alt')) == pq($row)->text()){
                        array_push($img_alt, pq($img)->attr('alt'));
                        array_push($images_src, pq($img)->attr('src'));
                    }
                }
                if(end($college_name) != end($img_alt)){
                    array_push($images_src, 'Without img');
                    array_push($img_alt, end($college_name));
                }
            }


            $dbname = 'allcollegies';
            $columns = array("college_id","image_src","college_name","city","state");
            foreach ($collegies_names as $key => $value) {
                $values = array($this->CollegiesID[$college_name[$key]],$images_src[$key],$college_name[$key],$city[$key],$state[$key]);
                $this->save($dbname,$columns,$values);
                $this->getCollege($this->CollegiesID[$college_name[$key]],$value);
            }
        }
        $this->deleteOldCollegies();
    }

    private function getCollege($college_id,$collegies_name)
    {
        $college_name =  pq($collegies_name)->text();// Название Колледжа
        $address; // Адрес
        $phone;// Телефон
        $site; // Сайт

        $link = pq($collegies_name)->attr('href');
        $url = ("https://www.princetonreview.com" . $link);
        $newDoc =  $this->grab($url);
        if($college_site = $newDoc->find('.school-headline-address a')->attr('href'))
            $site = $college_site;
        else
            $site = "Without site";
        $addr =  $newDoc->find('.row .col-xs-6.bold');
        $temp_address = false;
        $temp_phone = false;
        foreach ($addr as $value){
            $text = pq($value)->text();
            $text = preg_replace('/\s+/', '', $text);

            if(strcasecmp($text,"Address") == 0){
                $address = pq($value)->next()->text();
                $temp_address = true;
            }
            if(strcasecmp($text,"Phone") == 0){
                $phone = pq($value)->next()->text();
                $temp_phone = true;
            }
        }
        if(!$temp_address)
            $address = "Without address";
        if(!$temp_phone)
            $phone = "Without phone";

        $dbname = 'college';
        $columns = array('college_id','college_name','address','phone','site');
        $values = array($college_id,$college_name,$address,$phone,$site);
        $this->save($dbname,$columns,$values);
    }

    private function deleteOldCollegies()
    {
        $sql = "SELECT college_id FROM allcollegies";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $array_data = array_map('current', $result);
        $oldCollegies = array_diff($array_data,$this->CollegiesID);
        foreach ($oldCollegies as $row) {
            Yii::$app->db->createCommand("DELETE FROM allcollegies WHERE college_id = :college_id")->bindValue(':college_id',$row)->query();
            Yii::$app->db->createCommand("DELETE FROM college WHERE college_id = :college_id")->bindValue(':college_id',$row)->query();
        }
    }
}

<?php

require get_template_directory() . '/qinmei_animate/functions/chttochs/convert.php';
require get_template_directory() . '/qinmei_animate/phpQuery.php';
require_once get_template_directory() . "/qinmei_animate/functions/mains.php";

add_action( 'rest_api_init', function () {
	register_rest_route('wp/v2/', 'qinmei/search', array(
		'methods' => 'get',
		'callback' => 'aniamte_search',
	));
});


add_action( 'rest_api_init', function () {
	register_rest_route('wp/v2/', 'qinmei/getwebinfo', array(
		'methods' => 'POST',
		'callback' => 'aniamte_getinfo',
	));
});


function aniamte_search($request = null) {
	if(is_array($_GET)&&count($_GET)>0){
		if(isset($_GET["title"])){
			$title=RemoveXSS($_GET["title"]);
			if ($title!='') {
				//!image
				//iftype
				$iftype=iftype($title);
				if (substr_count($title,'type:')==1) {
					$scode=' type:'.getSubstr($title,' type:','/').'/';
					$title=str_replace($scode,'',$title);
				}
				//ifrun
				$ifrun=ifcode($title);
				if (substr_count($title,'only:')==1) {
					$scode=' only:'.getSubstr($title,' only:','/').'/';
					$title=str_replace($scode,'',$title);
				}
				if (substr_count($title,'exc:')==1) {
					$scode=' exc:'.getSubstr($title,' exc:','/').'/';
					$title=str_replace($scode,'',$title);
				}
				// 抓取网页
					//动画
				if ($iftype=='a') {
					$autotitle=whatstitle($title);
					$webd=asrh($title,$ifrun);
				// bangumi info
					//$r_info=infoS($webd[9]);
					//$n_info=$r_info[0];
					//$des_info=$r_info[1];
					//if ($des_info=='') {

					//}
				// bilibili 结果
				//if ($ifrun[0]=='true') {
					$r_bilibili=bilibiliS($webd[0]);
					$n_bilibili=$r_bilibili[2];
					$t_bilibili=$r_bilibili[0];
					$l_bilibili=$r_bilibili[1];
				//}
				// dilidili 结果
				if ($ifrun[1]=='true'){
					$r_dilidili=dilidiliS($webd[1],$autotitle,$title);
					$n_dilidili=$r_dilidili[2];
					$t_dilidili=$r_dilidili[0];
					$l_dilidili=$r_dilidili[1];
				}
				// xsjdm 结果
				if ($ifrun[9]=='true') {
					$r_xsjdm=baiduS($webd[10],'/{"title":"(.*?)动漫全集(.*?)","url":"(.*?)"}/',1,'x4jdm.com',$autotitle,$title);
					$n_xsjdm=$r_xsjdm[2];
					$t_xsjdm=$r_xsjdm[0];
					$l_xsjdm=$r_xsjdm[1];
					if ($n_xsjdm==0) {
						$r_xsjdm=baiduS($webd[10],'/{"title":"(.*?)","url":"(.*?)"}/',1,'x4jdm.com',$autotitle,$title);
						$n_xsjdm=$r_xsjdm[2];
						$t_xsjdm=$r_xsjdm[0];
						$l_xsjdm=$r_xsjdm[1];
					}
				}
				// fcdm 结果
				if ($ifrun[2]=='true'){
					$r_fcdm=fcdmS($webd[2]);
					$n_fcdm=$r_fcdm[2];
					$t_fcdm=$r_fcdm[0];
					$l_fcdm=$r_fcdm[1];
				}
				// pptv 结果
				if ($ifrun[3]=='true'){
					$r_pptv=pptvS($webd[3],$autotitle,$title);
					$n_pptv=$r_pptv[2];
					$t_pptv=$r_pptv[0];
					$l_pptv=$r_pptv[1];
				}
				// letv 结果
				if ($ifrun[4]=='true'){
					$r_letv=baiduS($webd[4],'/{"title":"(.*?)_全集(.*?)","url":"(.*?)"}/',1,'www.le.com',$autotitle,$title);// 1 参数暂时无用，下同
					$n_letv=$r_letv[2];
					$t_letv=$r_letv[0];
					$l_letv=$r_letv[1];
					if ($n_letv==0) {
						$r_letv=baiduS($webd[4],'/{"title":"(.*?)-在线观看-动漫(.*?)","url":"(.*?)"}/',1,'www.le.com',$autotitle,$title);// 1 参数暂时无用，下同
						$n_letv=$r_letv[2];
						$t_letv=$r_letv[0];
						$l_letv=$r_letv[1];
					}
				}
				// iqiyi 结果
				if ($ifrun[5]=='true'){
					$r_iqiyi=baiduS($webd[5],'/{"title":"(.*?)-动漫动画-全集(.*?)","url":"(.*?)"}/',1,'www.iqiyi.com',$autotitle,$title);
					$n_iqiyi=$r_iqiyi[2];
					$t_iqiyi=$r_iqiyi[0];
					$l_iqiyi=$r_iqiyi[1];
					if ($n_iqiyi==0) {
						$r_iqiyi=baiduS($webd[5],'/{"title":"(.*?)-全集在线观看-动漫(.*?)","url":"(.*?)"}/',1,'www.iqiyi.com',$autotitle,$title);
						$n_iqiyi=$r_iqiyi[2];
						$t_iqiyi=$r_iqiyi[0];
						$l_iqiyi=$r_iqiyi[1];
					}
					if ($n_iqiyi==0) {
						$r_iqiyi=baiduS($webd[5],'/{"title":"(.*?)-动漫-全集高清(.*?)","url":"(.*?)"}/',1,'www.iqiyi.com',$autotitle,$title);
						$n_iqiyi=$r_iqiyi[2];
						$t_iqiyi=$r_iqiyi[0];
						$l_iqiyi=$r_iqiyi[1];
					}
				}
				// youku 结果
				if ($ifrun[6]=='true'){
					$r_youku=baiduS($webd[6],'/{"title":"(.*?)—日本—动漫—优酷(.*?)","url":"(.*?)"}/',1,'www.youku.com',$autotitle,$title);
					$n_youku=$r_youku[2];
					$t_youku=$r_youku[0];
					$l_youku=$r_youku[1];
					if ($n_youku==0) {
						$r_youku=baiduS($webd[6],'/{"title":"(.*?)—日本—动漫(.*?)","url":"(.*?)"}/',1,'www.youku.com',$autotitle,$title);
						$n_youku=$r_youku[2];
						$t_youku=$r_youku[0];
						$l_youku=$r_youku[1];
					}
				}
				// 百度集合搜索 结果
				if ($ifrun[7]=='true'){
					$r_baiduall=baiduallS($webd[7]);
					$n_baiduall=$r_baiduall[2];
					$a_baiduall=$r_baiduall[1];
				}
				// 腾讯视频 结果
				if ($ifrun[8]=='true'){
					$r_tencenttv=baiduS($webd[8],'/{"title":"(.*?)-高清(.*?)","url":"(.*?)"}/',1,'v.qq.com',$autotitle,$title);
					$n_tencenttv=$r_tencenttv[2];
					$t_tencenttv=$r_tencenttv[0];
					$l_tencenttv=$r_tencenttv[1];
					if ($n_tencenttv==0) {
						$r_tencenttv=baiduS($webd[8],'/{"title":"(.*?)-动漫(.*?)","url":"(.*?)"}/',1,'v.qq.com',$autotitle,$title);
						$n_tencenttv=$r_tencenttv[2];
						$t_tencenttv=$r_tencenttv[0];
						$l_tencenttv=$r_tencenttv[1];
					}
				}
		        
		     

				//bilibili 保留示范
				//if ($ifrun[0]=='true') {
				$video = array();
		          
		        $bgmurl = "https://api.bgm.tv/search/subject/:".$title."?type=2";
		       	$bgmhtml = phpQuery::newDocumentFile($bgmurl);
		        $video['bgm'] = json_decode($bgmhtml,true);
		          
		          
				for ($i=0; $i<$n_bilibili; $i++) {
					$video[bilibili][]= array('link' =>$l_bilibili[$i+1],'title' => $t_bilibili[$i+1]);
				}
				for ($i=0; $i<$n_dilidili; $i++) {
					$video[dilidili][]= array('link' =>$l_dilidili[$i+1],'title' => $t_dilidili[$i+1]);
				}

				for ($i=0; $i<$n_xsjdm; $i++) {
					$video[xsjdm][]= array('link' =>$l_xsjdm[$i+1],'title' => $t_xsjdm[$i+1]);
				}

				for ($i=0; $i<$n_fcdm; $i++) {
					$video[fcdm][]= array('link' =>$l_fcdm[$i+1],'title' => $t_fcdm[$i+1]);
				}

				for ($i=0; $i<$n_pptv; $i++) {
					$video[pptv][]= array('link' =>$l_pptv[$i+1],'title' => $t_pptv[$i+1]);
				}

				for ($i=0; $i<$n_letv; $i++) {
					$video[letv][]= array('link' =>$l_letv[$i+1],'title' => $t_letv[$i+1]);
				}

				for ($i=0; $i<$n_iqiyi; $i++) {
					$video[iqiyi][]= array('link' =>$l_iqiyi[$i+1],'title' => $t_iqiyi[$i+1]);
				}

				for ($i=0; $i<$n_youku; $i++) {
					$video[youku][]= array('link' =>$l_youku[$i+1],'title' => $t_youku[$i+1]);
				}

				for ($i=0; $i<$n_tencenttv; $i++) {
					$video[tencenttv][]= array('link' =>$l_tencenttv[$i+1],'title' => $t_tencenttv[$i+1]);
				}


				return $video;

			}

	 	}
	}
}
}


function aniamte_getinfo($request = null) {

	$url = '';
	$type = '';
	$code = '';
	$year = '';
	$month = '';
	$kind = '';
	if(!empty($_POST['url'])){
	  $url = $_POST['url'];
	};

	if(!empty($_POST['type'])){
	  $type=$_POST['type'];
	}
	  
	if(!empty($_POST['kind'])){
	  $kind = $_POST['kind'];
	}

	if(!empty($_POST['code'])){
	   $code = $_POST['code']; 
	}
	  
	if(!empty($_POST['year'])){
	   $year = $_POST['year']; 
	}
	if(!empty($_POST['month'])){
	    $month = sprintf("%02d",$_POST['month'] );
	}

    if($kind == 'index'){
      return getindex($type,$url);
    }else if($kind == 'new'){
      return getnew($code,$year,$month);
    }else if($kind == 'validate'){
      return '404';
    }

}


function getindex($type,$url){
  switch($type){
    case 'dilidili':
          return getdilidili($url);
          break;
      case 'qq':
          return getqq($url);
          break;
      case 'iqiyi':
          return getiqiyi($url);
          break;
      case 'bilibili':
          return getbilibili($url);
          break;
      case 'letv':
          return getletv($url);
          break;
      case 'pptv':
          return getpptv($url);
          break;
      case 'bgm':
          return getbgm($url);
          break;
      default:
          return "参数错误";
  }

}

function getnew($code,$year,$month){
  switch($code){
    case 'qinmei2021':
      getjson($year,$month);
      break;
    default:
      echo "参数错误";
  }
}

function getdilidili($url){
    $html     = phpQuery::newDocumentFile($url);
    $hrefList = pq("(div[class='time_con']:eq(0) a)");

    $video = array();

    foreach ($hrefList as $href) {
        $hreflink = $href->getAttribute("href");
        $hrefhtml   = phpQuery::newDocumentFile($hreflink);
        $hreftitle =   pq("(div[id='link'] h2)",$hrefhtml)->contents()->not("a")->text();
        $videotitle = substr($hreftitle,6,strlen($hreftitle));
        $hrefplay =   pq("#player_iframe",$hrefhtml)->attr("src");'<br>';
        $videolink = 'http'.explode("=http",$hrefplay)[1];
        
        $video[] = array('title'=> $videotitle,'link' => $videolink);
    }
    
    //以json格式返回html页面
    return $video;
}

function getqq($url){
	$html     = phpQuery::newDocumentFile($url);
    $hrefList = pq("(div[class='mod_episode'] a)");

    $video = array();
    foreach ($hrefList as $href) {
        $hreflink = $href->getAttribute("href");
        $hreftitle =  pq($href)->find("span[itemprop='episodeNumber']")->text();
        $video[] = array('title'=> $hreftitle,'link' => $hreflink);
    }
    
    //以json格式返回html页面
    return $video;
}

function getiqiyi($url){
  	$html     = phpQuery::newDocumentFile($url);
    $hrefList = pq(".site-piclist:eq(0) li");
    $video = array();
    foreach ($hrefList as $href) {
        $hreflink = pq($href)->find('.site-piclist_info_describe a')->attr('href');
        $hreftitle =  trim(pq($href)->find('.site-piclist_info_describe a')->text());
      	$hreftitle0 = trim(pq($href)->find('.site-piclist_info_title a')->text());
        $video[] = array('title'=> $hreftitle0.$hreftitle,'link' => $hreflink);
    }
    
    return $video;
}

function getbilibili($url){
  	$showdata=file_get_contents($url);
    preg_match('#"episodes"([\s\S]*?)"evaluate"#',$showdata,$match);
    $data1 = $match[0];
    $data2 = substr($data1,11);
    $data3 = trim(substr($data2,0,strlen($data2)-11));
    $data4 =  json_decode($data3,true);

    $video = array();
      foreach ($data4 as $href) {
          $hreflink = 'https://www.bilibili.com/video/av'.$href['aid'].'/';
          $hreftitle =  $href['index_title'];
          $video[] = array('title'=> $hreftitle,'link' => $hreflink);
      }

    return $video;
}

function getletv($url){
  	$html     = phpQuery::newDocumentFile($url);
    $hrefList = pq("#first_videolist .show_cnt  .col_4");
    $video = array();
    foreach ($hrefList as $href) {
        $hreflink = pq($href)->find('.d_tit a')->attr('href');
        $hreftitle =  trim(pq($href)->find('.d_tit a')->attr('title'));
        $video[] = array('title'=> $hreftitle,'link' => $hreflink);
    }
    
    return $video;
}

function getpptv($url){
  	$showdata=file_get_contents($url);
    preg_match('#"now":0,"dlist":([\s\S]*?)}]}},{"id"#',$showdata,$match);
	$data1 = $match[0];
	$data2 = substr($data1,16);
	$data3 = trim(substr($data2,0,strlen($data2)-8));
	echo $data3;
}

function getbgm($url){
    $url1 = 'https://bangumi.tv/subject/'.$url;
    $html     = phpQuery::newDocumentFile($url1);
    $hrefList = pq(".subject_tag_section .inner  a");
    $videotag = '';
    foreach ($hrefList as $href) {
       $videotag = $videotag.(pq($href)->find("span")->text()).',';
    }
    $videotag = substr($videotag,0,strlen($videotag)-1);
  
  	$url2 = request('https://api.bgm.tv/subject/'.$url.'?responseGroup=large',array('aaa'=>'aaa'));
	$data = json_decode($url2,true);
  	$data['tag'] = $videotag;
  	return $data;
  
}


function getjson($year,$month){
  $index = get_template_directory() . '/qinmei_animate/bangumi-data/data/items/'.$year.'/'.$month.'.json';
  $indexnum = $year.$month.'000';
  $data= file_get_contents($index); 
  $animate = array(
    "num" => $indexnum,
    "qinmei2021"=> json_decode($data)
    );
  return $animate;
}



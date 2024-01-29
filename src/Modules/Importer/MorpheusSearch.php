<?php
namespace Morpheus\Modules\Importer;
define('__IPMHOME__',substr(__DIR__,0,strpos(__DIR__,'wp-content')));
require_once 'SearchImporter.php';
// require __IPMHOME__.'wp-load.php';
require __IPMHOME__.'wp-admin/includes/admin.php';
class MorpheusSearch{
 private function getCategories($a){
  $r=[];
  foreach($a as $v)
   array_push($r,$this->getCategory($v));
  return $r;
 }
 private function getCategory($n){
  $a='name';$b='description';$c='parent';$i=get_cat_ID($n[$a]);
  if(!!$i)return $i;
  return wp_insert_category([
   'taxonomy'=>'category',
   'cat_name'=>$n[$a],
   'category_nicename'=>$n[$a],
   'category_description'=>(isset($n[$b])?$n[$b]:''),
   'category_parent'=>(
    isset($n[$c])&&isset($n[$c][$a])?
    $this->getCategory($n[$c]):
    0
   )
  ]);
 }
 private function insertPost($d){
  $a='tags';
  $b='seotitle';
  $c='seodescription';
  $f='credits';
  $d['title']=wp_strip_all_tags($d['title']);
  $h=[
   'ID'=>(get_post($d['id'])!==NULL?$d['id']:0),
   'post_name'=>$d['slug'],
   'post_title'=>$d['title'],
   'post_content'=>$d['content'],
   'post_status'=>$d['status'],
   'post_author'=>get_current_user_id(),
   'post_category'=>$this->getCategories($d['categories']),
   'tags_input'=>(isset($d[$a])?$d[$a]:[]),
   'tax_input'=>(isset($d[$f])?['credit'=>$d[$f]]:[])
  ];
  $p=wp_insert_post($h);
  if(isset($d[$b])&&isset($d[$c])){
   update_post_meta($p,'_yoast_wpseo_title',$d[$b]);
   update_post_meta($p,'_yoast_wpseo_metadesc',$d[$c]);
  }
  $r=$this->getAttachmentByUrl($d['thumbnail']);
  if(!!$r)$i=$r->ID;
  else $i=wp_insert_attachment([
   'guid'=>$d['thumbnail'],
   'post_mime_type'=>wp_check_filetype(basename($d['thumbnail']),null)['type'],
   'post_title'=>preg_replace('/\.[^.]+$/','',basename($d['thumbnail'])),
   'post_content'=>'',
   'post_status'=>'inherit'
  ],$f);
  set_post_thumbnail($p,$i);
  if(isset($d[$e])){
   $w=insertImage($u,$g,$h,$d[$e]);
   set_post_thumbnail($p,$w);
  }
  $h['stateId']=$p;
  $h['url']=get_permalink($p);
  return $h;
 }
 public function import(){
  $d=json_decode(file_get_contents('php://input'),true);
  echo json_encode($this->insertPost($d),JSON_PRETTY_PRINT);
 }
 private function getAttachmentByUrl($u){
  global $wpdb;
  $r=$wpdb->get_results('SELECT * FROM '.$wpdb->posts.' WHERE post_type="attachment" AND guid="'.$u.'";');
  if(!$r||!isset($r[0]))return false;
  return $r[0];
 }
 public function __construct(){
  add_action('admin_menu',[$this,'menu']);
  add_action('admin_enqueue_scripts',[$this,'load_scripts']);
  add_action('admin_init',[$this,'start']);
  add_filter('admin_footer_text','__return_empty_string',11);
  add_filter('update_footer','__return_empty_string',11);
  add_filter('wp_get_attachment_url',function($u,$i){return get_post($i)->guid;},99,2);
  add_action('rest_api_init',[$this,'rest_api']);
 }
 public function rest_api(){
  register_rest_route('morpheus','import',[
   'methods'=>'POST',
   'callback'=>[$this,'import'],
   'permission_callback' => '__return_true',
  ]);
 }
 public function load_scripts(){
  wp_enqueue_script('script',plugin_dir_url(__FILE__).'includes/js/js.js');
 }
 public function start(){
  wp_enqueue_style('style',plugin_dir_url(__FILE__).'includes/css/css.css');
 }
 public function menu(){
  add_menu_page(
   'Morpheus Search',
   'Morpheus Search',
   'read',
   'content-search',
   [$this,'searchPage'],
   'dashicons-search',
   5
  );
 }
 public function searchPage(){
  return SearchImporter::page();
 }
 function remove_footer_admin(){return '';}
}

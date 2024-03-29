<?php
namespace Morpheus\Shared\Classes;
use stdClass;
class ContentBlocks{
  public $removeContent=[
  'core/columns',
  'core/column',
  'core/cover',
  'core/image',
  'core/list'
  ];
  public function getAttributes($s,$t){
    $r=[];$n=[];$m=null;
    if(preg_match('/<'.$t.'[^>]+>(.+)<\/'.$t.'>/is',$s,$m))
    $r['#text']=$m[1]??null;
    preg_match('/<'.$t.'[^>]*>/i',$s,$m);
    if($m){
      $n=$m[0];
      preg_match_all('/\s*([^\s=]+)\s*=\s*[\"\'](.*?)[\"\']/sm',$n,$m);
      for($i=0;$i<count($m[1]);$i++)
        $r[$m[1][$i]]=($m[2][$i]??null);
    }
    return $r;
  }
  public static function getAttachmentByUrl($u){
   global $wpdb;
   $r=['id'=>0,'credit'=>null];
   If($u){
    $q=$wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key=\"_wp_attachment_metadata\" AND meta_value LIKE \"%$u%\";");
    if($q&&isset($q[0])){
     $r['id']=$q[0]->post_id;
     $q=$wpdb->get_results("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key=\"image_credit\" AND post_id=\"{$r['id']}\";");
     if($q&&isset($q[0]))$r['credit']=$q[0]->meta_value;
    }
   }
   return $r;
  }
  public function parseBlock(object $block, array $blockOriginal):stdClass{
    if($block->name==='core/paragraph')
      $block->content=preg_replace('/^<p>(.*)<\/p>$/',"$1",$block->content);
    elseif($block->name==='core/list-item')
      $block->content=preg_replace('/^<li>(.*)<\/li>$/',"$1",$block->content);
    elseif($block->name==='core/heading'){
      preg_match('/^<h(?<size>[1-6]{1})[^>]*>.*<\/h[1-6]{1}>$/',$block->content,$m);
      $a=$this->getAttributes($block->content,'a');
      $block->props=array_merge($block->props,array_filter([
        'size'=>$m['size']??'',
        'href'=>$a['href']??'',
        'target'=>$a['target']??''
      ]));
      $block->content=strip_tags($block->content);
    }elseif($block->name==='core/image'){
      $a=$this->getAttributes($block->content,'img');
      $b=$this->getAttributes($block->content,'figcaption');
      $c=$this->getAttributes($block->content,'a');
      $f=$this->getAttachmentByUrl(preg_replace("/https?:\/\/[^\/]+/","",$a['src']));
      $block->props=array_merge($block->props,array_filter([
        'source'=>$a['src']??'',
        'alt'=>$a['alt']??'',
        'credit'=>$f['credit']??'',
        'caption'=>$b['#text']??'',
        'href'=>$c['href']??'',
        'target'=>$c['target']??''
      ]));
    }
    $this->blockRemoveContent($block);
    return apply_filters('morpheus_parse_block',$block,$blockOriginal);
  }
  private function blockRemoveContent(object &$block){
    if(!in_array($block->name,apply_filters('morpheus_block_remove_content',$this->removeContent)))return;
    unset($block->content);
  }
  private function parseReusableBlock(object $block){
    if($block->name!=='core/block')return $block;
    $p=\WP_Post::get_instance($block->props['ref']);
    return $this->getBlocks($p->post_content);
  }
  public function parseBlocks(array $blocks):array{
    $w=[];
    $blocks=array_filter($blocks,function($b){return $b['blockName']!==null;});
    foreach($blocks as $o){
      $b=$this->parseReusableBlock($this->parseBlock($this->parseBlockBase($o),$o));
      if(is_array($b))$w=array_merge($w,$b);
      else array_push($w,$b);
    }
    return array_values($w);
  }
  private function parseBlockBase(array $blockOriginal):stdClass{
    $b=(object)$blockOriginal;
    if(isset($b->innerContent))unset($b->innerContent);
    if($b->blockName){
      $b->name=$b->blockName;
      unset($b->blockName);
    }
    $b->props=[];
    if(!empty($b->attrs))$b->props=$b->attrs;
    unset($b->attrs);
    if(!empty($b->innerBlocks))$b->children=$this->parseBlocks($b->innerBlocks);
    unset($b->innerBlocks);
    if(!empty($b->innerHTML))$b->content=preg_replace('/\n/','$1',$b->innerHTML);
    unset($b->innerHTML);
    return $b;
  }
  public function getBlocks(string $content):array{
    return $this->parseBlocks(parse_blocks($content));
  }
  public static function factory(){return new ContentBlocks();}
}

<?php
namespace Morpheus\Modules\Importer;
class SearchImporter{
 public static function page(){
  $c=get_categories(['hide_empty'=>0]);
?>
<div class="backloading"><div class="loaderg"></div></div>
<h1 class="ui header">Buscador de Conteúdo</h1>
<script>
 importer.page=1;
 importer.totalPages=0;
 importer.length=50;
 importer.loading=document.getElementsByClassName('backloading')[0];
 importer.auth.url='<?php echo plugin_dir_url(__FILE__)?>';
 importer.auth.nounce='<?php echo wp_create_nonce('wp_rest')?>';
 importer.elastic.url='<?php echo get_option('options_hermes_elasticsearch_endpoint','')?>';
 importer.elastic.index='<?php echo get_option('options_hermes_elasticsearch_index','')?>';
 importer.elastic.user='<?php echo get_option('options_hermes_elasticsearch_user','')?>';
 importer.elastic.pass='<?php echo get_option('options_hermes_elasticsearch_pass','')?>';
 importer.s3.url='<?php echo get_option('options_hermes_s3_endpoint','')?>';
 importer.home='<?php echo get_site_url()?>';
 const getSearch=()=>{
   const f=document.getElementById('formOptions'),
    i=f.getElementsByTagName('input'),
    s=f.getElementsByTagName('select');
   return [i[0].value.trim(),s[1].value.trim(),s[0].value.trim()]
  },
  search=async p=>await importer.search(p||1,...getSearch()),
  previous=async()=>await importer.previous(...getSearch()),
  next=async()=>await importer.next(...getSearch());
</script>
<div class="modal" onclick="importer.modal.close()"><div id="modal"><div><div></div><div class="close" onclick="importer.modal.close()">X</div></div><div></div></div></div>
<form id="formOptions" onsubmit="search();return false">
 <!-- Opções de busca -->
 <div class="row">
  <div class="column" style="width:100%;padding-right:20px">
   <label>Pesquisar Matérias</label>
   <input type="text" class="IPMinput" style="width:100%">
  </div>
</div><div class="row">
  <div class="column">
   <label>Geolocalização</label>
   <select class="IPMselect">
    <option value="" selected>Todas as Regiões</option>
    <option value="nacional">Nacional</option>
    <option value="parana">Paraná</option>
    <option value="curitiba">Curitiba</option>
   </select>
  </div>
  <div class="column">
   <label>Editorias</label>
   <select class="IPMselect">
    <option value="" selected>Todas as categorias</option>
<?php
 foreach ($c as $x)
  echo '    <option value="'.$x->slug.'">'.$x->name.'</option>';
?>
   </select>
  </div>
  <div class="column">
   <label>&nbsp;</label>
   <input type="submit" value="pesquisar" class="IPMinput">
  </div>
 </div>
</form>
<div class="datatable" style="padding-right:20px"><table><tr><th>Data</th><th>Título</th><th>Editoria</th><th>Autor</th><th>Importar</th></tr></table></div>
<div class="pagination">
 <input type="button" value="&#x21E6;" onclick="previous()" class="IPMinput">
 <select onchange="search(this.value)" class="IPMselect"></select>
 <input type="button" value="&#x21E8;" onclick="next()" class="IPMinput">
</div>
<?php }} ?>
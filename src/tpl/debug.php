<style type="text/css">
    .ke-debug{
        position: fixed;
        background: #fff;
        width: 100%;
        box-sizing: border-box;
        bottom: 0;
        font-size: 14px;
    }
    .ke-debug a{
        text-decoration: none;
        color: #1E9FFF;
    }
    .ke-debug p{
        margin: 0;
        padding: 5px;
    }
    .ke-debug .li{
        display: none;
        box-sizing: border-box;
        background: #fff9ec;
        position: fixed;
        padding: 5px;
        bottom: 25px;
        width: 100%;
        height: 350px;
        overflow: auto;
        border-bottom: solid 1px #0C0C0C;
    }
</style>
<div class="ke-debug">
    <p>
        [ <a href="javascript:loadFiles();">加载文件</a> ] <?php echo count($included_files);?>
    </p>
    <div id="loadfiles" class="li">
        <?php foreach ($included_files as $i=>$item):?>
        <div><?php echo $i;?>/<?php echo $item;?></div>
        <?php endforeach;?>
    </div>
</div>
<script type="text/javascript">
    function loadFiles() {
        var id=document.getElementById('loadfiles');
        if(id.style.display=='block'){
            id.style.display="none";
        }else{
            id.style.display="block";
        }
    }
</script>
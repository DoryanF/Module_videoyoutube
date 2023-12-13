<form method="post" action="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&id_product={$product}&updateproductvideo=1&token={$token}" name="formProductVideo" id="formProductVideo">
    <div class="form-group">
        <label class="form-control-label" for="url_video">URL Video: </label>
        <input type="text" class="form-control" id="url_video" name="url_video_product" placeholder="url"/>
        <input type="hidden" value="{$product}" name="product"/>
    </div>
    <button class="btn btn-primary" name="submitFormProductVideo" type="submit">Save</button>
</form>

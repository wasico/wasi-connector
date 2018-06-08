<div id="wasiSearchApp" class="wasi_search">
    <form action="/<?php echo $properties_slug; ?>" method="GET" role="form" class="<?php echo $instance['formClass'] ?>"
        v-on:submit.prevent="wasiSearchProperties">
        
        <div class="form-group">
            <label for="keyword-match"><?php _e('Keyword:', 'wasico'); ?></label>
            <div class="btn-group bootstrap-select">
                <input id="keyword-match" placeholder="<?php _e('Keyword', 'wasico'); ?>" name="match" type="text" class="form-control inp-text" v-model="filters.match">
            </div>
        </div>

        <div class="form-group">
            <label for=""><?php _e('Looking for:', 'wasico'); ?></label>
            <div class="btn-group bootstrap-select">
                <select class="selectpicker" name="for_type" id="for_type" v-model="filters.for_type">
                    <option value="0"><?php _e('All', 'wasico'); ?></option>

                    <?php foreach ($propertyStatus as $key => $status) {
                        echo '<option value="'.$key.'">'.$status.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="id_property_type"><?php _e('Type:', 'wasico'); ?></label>
            <div class="btn-group bootstrap-select">
                <select class="selectpicker" name="id_property_type" id="id_property_type" v-model="filters.id_property_type">
                    <option value="0"><?php _e('All', 'wasico'); ?></option>
                    <?php foreach ($propertyTypes as $type) {
                        echo '<option value="'.$type->id_property_type.'">'.$type->nombre.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <!-- <div class="form-group col-md-2 col-sm-4 col-xs-6">
            <label for="">Ciudad:</label>
            <div class="btn-group bootstrap-select">
                <select class="selectpicker" data-live-search="true" name="ciudad" id="ciudad" onchange="cargar_localidad('ciudad','zonas', 'zonas', true)" tabindex="-98">
                    <option value="0">Todas las ubicaciones</option>
                </select>
            </div>
        </div>
        <div class="form-group col-md-2 col-sm-4 col-xs-6">
            <label for="zones">Zona:</label>
            <div class="btn-group bootstrap-select">
                <select class="selectpicker" name="zone" id="zones">
                    <option value="0" selected="selected">Todos</option>
                </select>
            </div>
        </div> -->
        <div class="form-group">
            <label for="min_bedrooms"><?php _e('Bedrooms:', 'wasico'); ?></label>
            <div class="btn-group bootstrap-select">
                <select class="selectpicker" name="min_bedrooms" id="min_bedrooms" v-model="filters.min_bedrooms">
                    <option value="0"><?php _e('All', 'wasico'); ?></option>
                    <?php for ($i=1; $i<=10; $i++) {
                        echo '<option value="'.$i.'">'.$i.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="bathrooms"><?php _e('Bathrooms:', 'wasico') ?></label>
            <div class="btn-group bootstrap-select">
                <select class="selectpicker" name="bathrooms" id="bathrooms" v-model="filters.bathrooms">
                    <option value="0"><?php _e('All', 'wasico'); ?></option>
                    <?php for ($i=1; $i<=5; $i++) {
                        echo '<option value="'.$i.'">'.$i.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label></label>
            <label><?php _e('Prices range:', 'wasico') ?></label>
            <div class="<?php echo $instance['formClass'] ?>" style="padding: 0px;">
                <div class="col-xs-6 span6" style="padding: 1px;">
                    <input id="min_price" placeholder="<?php _e('Price min', 'wasico'); ?>" name="min_price" type="number" class="form-control inp-text" v-model="filters.min_price">
                </div>
                <div class="col-xs-6 span6" style="padding: 1px;">
                    <input id="max_price" placeholder="<?php _e('Price max', 'wasico'); ?>" name="max_price" type="number" class="form-control inp-text" v-model="filters.max_price">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label></label>
            <label><?php _e('Area range:', 'wasico') ?></label>
            <div class="<?php echo $instance['formClass'] ?>" style="padding: 0px;">
                <div class="col-xs-6 span6" style="padding: 1px;">
                    <input id="min_area" placeholder="<?php _e('Area min', 'wasico'); ?>" name="min_area" type="number" class="form-control inp-text" v-model="filters.min_area">
                </div>
                <div class="col-xs-6 span6" style="padding: 1px;">
                    <input id="max_area" placeholder="<?php _e('Area max', 'wasico'); ?>" name="max_area" type="number" class="form-control inp-text" v-model="filters.max_area">
                </div>
            </div>
        </div>

        <div class="form-group col-xs-12">
            <button id="search-btn" class="<?php echo $instance['submitClass'] ?>"
                data-cleaned-text="!!" data-loading-text="<?php _e('Searching', 'wasico'); ?>..."><?php _e('Search'); ?></button>
        </div>
    </form>
</div>
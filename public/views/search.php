<div id="wasiSearchApp" class="wasi_search">
    <form action="/<?php echo $properties_slug; ?>" method="GET" role="form" class="<?php echo $instance['formClass'] ?>"
        v-on:submit.prevent="wasiSearchProperties">
        
        <div class="form-group">
            <label for="keyword-match"><?php _e('Keyword:', 'wasico'); ?></label>
            <input id="keyword-match" placeholder="<?php _e('Keyword', 'wasico'); ?>" name="match" type="text" class="form-control inp-text" v-model="filters.match">
        </div>

        <div class="form-group">
            <label for=""><?php _e('Looking for:', 'wasico'); ?></label>
            <select class="selectpicker" name="for_type" id="for_type" v-model="filters.for_type">
                <option value="0"><?php _e('All', 'wasico'); ?></option>

                <?php foreach ($propertyStatus as $key => $status) {
                    echo '<option value="'.$key.'">'.$status.'</option>';
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_property_type"><?php _e('Type:', 'wasico'); ?></label>
            <select class="selectpicker" name="id_property_type" id="id_property_type" v-model="filters.id_property_type">
                <option value="0"><?php _e('All', 'wasico'); ?></option>
                <?php foreach ($propertyTypes as $type) {
                    echo '<option value="'.$type->id_property_type.'">'.$type->nombre.'</option>';
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="min_bedrooms"><?php _e('Bedrooms:', 'wasico'); ?></label>
            <select class="selectpicker" name="min_bedrooms" id="min_bedrooms" v-model="filters.min_bedrooms">
                <option value="0"><?php _e('All', 'wasico'); ?></option>
                <?php for ($i=1; $i<=10; $i++) {
                    echo '<option value="'.$i.'">'.$i.'</option>';
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="bathrooms"><?php _e('Bathrooms:', 'wasico') ?></label>
            <select class="selectpicker" name="bathrooms" id="bathrooms" v-model="filters.bathrooms">
                <option value="0"><?php _e('All', 'wasico'); ?></option>
                <?php for ($i=1; $i<=5; $i++) {
                    echo '<option value="'.$i.'">'.$i.'</option>';
                } ?>
            </select>
        </div>
        
        <!-- LOCALE FILTERS BY: Country/Region/City/Zone -->
        <div class="form-group">
            <label for="contact-country"><?php _e('Your country', 'wasico'); ?>:</label>
            <select class="selectpicker" name="contact-country" id="contact-country" 
                v-model="contact.id_country" v-on:change="changeLocationCountry">
                <option value="0"><?php _e('Select country', 'wasico'); ?></option>
                <?php foreach ($wasiCountries as $country) {
                    echo '<option value="'.$country->id_country.'">'.$country->name.'</option>';
                } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="contact-region"><?php _e('Region', 'wasico'); ?>:</label>
            <select class="selectpicker" name="contact-region" id="contact-region" 
                v-model="contact.id_region" v-on:change="changeLocationRegion">
                <option v-for="region in location.regions" v-bind:value="region.id_region">
                    {{region.name}}
                </option>
            </select>
        </div>
        <div class="form-group">
            <label for="contact-city"><?php _e('City', 'wasico'); ?>:</label>
            <select class="selectpicker" name="contact-city" id="contact-city" 
                v-model="contact.id_city" v-on:change="changeLocationCity">
                <option v-for="city in location.cities" v-bind:value="city.id_city">
                    {{city.name}}
                </option>
            </select>
        </div>
        <div class="form-group">
            <label for="contact-zone"><?php _e('Zone', 'wasico'); ?>:</label>
            <select class="selectpicker" name="contact-zone" id="contact-zone" 
                v-model="contact.id_zone">
                <option v-for="zone in location.zones" v-bind:value="zone.id_zone">
                    {{zone.name}}
                </option>
            </select>
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
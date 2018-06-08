<div id="wasiApp" class="listings-row">
    <div v-show="app_ready===true && loading===false && properties.length<=0" class="listings-not-found text-center" v-cloak>
        <h3><?php _e('Properties not found!', 'wasico'); ?></h3>
        <span><?php _e('Try again with different filters.', 'wasico'); ?></span>
    </div>
    <div v-show="app_ready===true && loading===true" class="listings-not-found text-center" v-cloak>
        <h3><?php _e('Searching properties...', 'wasico'); ?></h3>
    </div>
    <article class="listing-container" v-for="prop in properties" v-cloak>
        <div class="listing-row"> <!-- class featured in case of define any way to detect it -->
            <div class="listing-row-image" v-bind:style="prop.thumbnail">
                <a v-bind:href="'/<?php echo $atts['propertyPage']; ?>/'+prop.id_property" class="listing-row-image-link"></a>
                <!-- <div class="listing-row-label-top listing-row-label-top-left">Featured</div> -->
                <div class="listing-row-label-bottom">{{property_types[prop.id_property_type].name}}</div>
            </div>
            <div class="listing-row-properties">
                <h3 class="listing-row-title"><a v-bind:href="'/<?php echo $atts['propertyPage']; ?>/'+prop.id_property" >{{prop.title}}</a></h3>
                <address v-html="prop.city_label+', '+prop.region_label"></address>
                <dl>
                    <dt><?php _e('Rooms', 'wasico'); ?></dt>
                    <dd>{{prop.bedrooms}}</dd>
                    <dt><?php _e('Bathrooms', 'wasico'); ?></dt>
                    <dd>{{prop.bathrooms}}</dd>
                </dl>
            </div>
            <div class="listing-row-properties">
                <dl>
                    <dt v-if="prop.for_rent=='true' && prop.rent_price>0"><?php _e('Rent price', 'wasico'); ?></dt>
                    <dd v-if="prop.for_rent=='true' && prop.rent_price>0">
                        <span>{{prop.rent_price | formatNumber}}</span>
                    </dd>
                    <dt v-if="prop.for_sale=='true' && prop.sale_price>0"><?php _e('Sale price', 'wasico'); ?></dt>
                    <dd v-if="prop.for_sale=='true' && prop.sale_price>0">
                        <span>{{prop.sale_price | formatNumber}}</span>
                    </dd>
                    <dt><?php _e('Area', 'wasico') ?></dt>
                    <dd>{{prop.area}} {{prop.unit_built_area_label}}</dd>
                </dl>
            </div>
        </div>
    </article>
    <!-- pagination: -->
    <nav aria-label="Page navigation" class="nav-container pagination hidden" 
        v-show="total_pages>0 && total_properties>properties_per_page" v-cloak>
      <ul class="pagination pagination-inner">
        <li>
          <a href="#" aria-label="Previous" v-on:click="previousPage()">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li v-for="page in total_pages" v-bind:class="activePageClass(page)">
            <a href="#" v-on:click.stop="paginate(page)">{{page}}</a>
        </li>
        <li>
          <a href="#" aria-label="Next" v-on:click="nextPage()">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
</div>
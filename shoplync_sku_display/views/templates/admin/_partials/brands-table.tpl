<table id="brands-table" class="table">
<thead class="thead-default">
    <tr class="column-headers ">
        <th scope="col">ID</th>
        <th scope="col">Brand</th>
        <th scope="col" class="text-center">Hide MPN</th>
        <th scope="col" class="text-center">Hide SKU</th>
        <th scope="col"></th>
    </tr>
    <tr class="column-filters ">
        <td></td>
        <td></td>
        <td><button id="mpn-check" type="button" class="btn btn-secondary btn-block">Check/Uncheck All</button></td>
        <td><button id="sku-check" type="button" class="btn btn-secondary btn-block">Check/Uncheck All</button></td>
        <td></td>
    </tr>
</thead>
<tbody>
{foreach from=$brands item=brand}
  {*include file='catalog/_partials/miniatures/brand.tpl' brand=$brand*}
  <tr>
      <td>{$brand.id_manufacturer}</td>
      <td>{$brand.name} - ({$brand.nb_products})</td>
      <td><input type="checkbox" title="mpn-check" class="block mx-auto" name="mpn[]" value="{$brand.id_manufacturer}" {if isset($brand.mpn_hide) && $brand.mpn_hide == 1}checked{/if}></td>
      <td><input type="checkbox" title="sku-check" class="block mx-auto" name="sku[]" value="{$brand.id_manufacturer}" {if isset($brand.sku_hide) && $brand.sku_hide == 1}checked{/if}></td>
      <td>
        <button type="button" class="btn btn-secondary" onclick="CheckRow(this)">Hide/Unhide Both</button>
      </td>
  </tr>
{/foreach}
</tbody>
</table>
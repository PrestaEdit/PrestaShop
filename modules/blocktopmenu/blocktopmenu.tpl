{if $MENU != ''}
	</div>

	<!-- Menu -->
	<div class="sf-contener clearfix">
		<ul class="sf-menu clearfix">
			{$MENU}
			{foreach from=$items item=menu name=menu_items}
				<li {$menu.selected}><a href="{$menu.link}" {if $menu.new_window}target="_blank"{/if}>{$menu.name}</a></li>
			{/foreach}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
		<div class="sf-right">&nbsp;</div>

	<!--/ Menu -->
{/if}
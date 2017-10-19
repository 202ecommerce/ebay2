<div id="popin-mode_boost" class="popin popin-lg" style="display: none;">
    <div class="panel">
        <div  class="panel-heading">
            <i class="icon-rocket"></i> {l s="Mode Boost" mod="ebay"}
            <span class="badge badge-success"><span class="page_popin_boost" id="1">1</span> / 2</span>
        </div>

        <form action="" class="form-horizontal" method="post" >
            <div id="1" class="page_boost selected first_page_popin">
                <p>{l s="Boost mode allows for 5 tasks to be proceeded simultaneously. It's suited when you have a large amount of tasks."}</p>
                <div class='alert alert-warning'>
                    <button class='close' data-dismiss='alert'>&#215;</button>
                    {l s="After launching boost mode, you must keep your browser window open." mod="ebay"}
                </div>
            </div>
            <div id="2" class="page_boost" style="display: none">
                <p class="big">{l s="Synchronisation in progress"}</p>

                <div class="progressbar">
                    <div class="tasks">
                        <strong><span class="value valueDone">0</span> / <span class="value valueMax">{l s="loading..."}</span> {l s="tasks"}</strong>
                        <strong class="percentages">{l s="loading..."}</strong>
                    </div>
                    <div class="line-wrapper">
                        <div class="line percentages_line" style="width: 0%;"></div>
                    </div>
                </div>

                <div class='alert alert-warning'>
                    <button class='close' data-dismiss='alert'>&#215;</button>
                    {l s="You must keep your browser window open else synchronisation will stop." mod="ebay"}
                </div>
            </div>
        </form>

        <div class="panel-footer">
            <button class="js-close-popin_boost btn btn-default"><i class="process-icon-cancel"></i>{l s='Stop Boost' mod='ebay'}</button>
            <button class="js-next-popin_boost btn btn-primary pull-right star_boost"><i class="process-icon-next"></i>{l s='Start' mod='ebay'}</button>

        </div>
    </div>
</div>
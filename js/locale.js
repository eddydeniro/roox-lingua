const module_name = 'locale';
function waitForElm(selector) {
    return new Promise(resolve => {
    if (document.querySelector(selector)) {
        return resolve(document.querySelector(selector));
    }

    const observer = new MutationObserver(mutations => {
        if (document.querySelector(selector)) {
        resolve(document.querySelector(selector));
        observer.disconnect();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    });
}
const urlSearchParams = new URLSearchParams(window.location.search);
const params = Object.fromEntries(urlSearchParams.entries());

if(params.module=='items/items'){
    $.ajax({
    url: 'index.php?module='+plugin_name+'/'+module_name+'/&action=get_data&token='+sess_token,
    method: 'POST',
    data: {path: params.path, page: 'items'}
    }).done(function(respons){
    if(respons!='false'){
        const dt = JSON.parse(respons);
        $('h3.page-title').text(dt['name']);
        waitForElm('.table-scrollable.table-wrapper.slimScroll').then(function(){
        

        // for (i = 0; i < dt.length; i++){
        //   const d = dt[i];
        //   if(d.name){
        //     $('.field-' + d.field_id + '-th').html('<div>'+d.name+'</div>');
        //   }
        // }                    
        });
    }
    });
}
if(params.module=='items/info'){
    $.ajax({
        url: 'index.php?module='+plugin_name+'/'+module_name+'/&for=get_data',
        method: 'POST',
        data: {path: params.path, page: 'info'}
    }).done(function(respons){
        if(respons!='false'){
            const dt = JSON.parse(respons);
            waitForElm('.navbar-header>a.navbar-brand').then(function(){
            $('.navbar-header>a.navbar-brand').text(dt['name']);
            })
            //Info Page
            //'.navbar-header>.navbar-brand' FOR Project Info
            waitForElm('.table-scrollable.table-wrapper.slimScroll').then(function(){
            

            // for (i = 0; i < dt.length; i++){
            //   const d = dt[i];
            //   if(d.name){
            //     $('.field-' + d.field_id + '-th').html('<div>'+d.name+'</div>');
            //   }
            // }                    
            });
        }
    });
}    

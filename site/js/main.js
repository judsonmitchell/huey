//initial focus
$('input').focus();

//function to get query string values http://goo.gl/8Rvxq
$.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return $.getUrlVars()[name];
  }
});

function queryHuey(type,search)
{
    $.get('http://hueylaw.org/api',{'type' : type, 's' : search}, function(data){
        var serverResponse = $.parseJSON(data);
        var source = $("#each-template").html();
        var template = Handlebars.compile(source);
        $("#results").html(template({laws:serverResponse}));
        History.pushState(data,'Huey - ' + serverResponse[0].title ,'?type=' + type + '&s=' + search);

    })
    .error(function(jqXHR, textStatus, errorThrown){
        var serverResponse = $.parseJSON(jqXHR.responseText); 
        var source = $('#error-template').html();
        var template = Handlebars.compile(source);
        $('#results').html(template(serverResponse));
    });

}

//see if url contains search parameters; if so, display
var urlVars = $.getUrlVars('name');
if (typeof urlVars.type !== 'undefined')
{
   queryHuey(urlVars.type,decodeURIComponent(urlVars.s)); 
}

//handle search term from input
$(document).keypress(function(e) {
    if(e.which == 13) {
        var searchTerm = $('input').val();
        queryHuey('fuzzy',searchTerm);
    }
});

//request specific document
$(document).on('click', 'a.detail', function(event){
    event.preventDefault();
    var searchTerm = $(this).attr('docid');
    queryHuey('docid',searchTerm);
});






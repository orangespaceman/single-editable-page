tinyMCE.init({
    // General options
    width     : "100%",
    mode      : "exact",
    elements  : "content",
    theme     : "advanced",
    plugins   : "autolink,lists,inlinepopups,preview,searchreplace,contextmenu,paste,wordcount,advlist,autosave",

    // Theme options
    theme_advanced_buttons1           : "bold,italic,formatselect,|,cut,copy,paste,|,search,replace,|,bullist,numlist,|,link,unlink,anchor,image",
    theme_advanced_buttons2           : "undo,redo,|,preview,|,code",
    theme_advanced_buttons3           : "",
    theme_advanced_buttons4           : "",
    theme_advanced_toolbar_location   : "top",
    theme_advanced_toolbar_align      : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing           : true,

    // Example content CSS (should be your site CSS)
    content_css : "_includes/css/site.css",

    // Drop lists for link/image/media/template dialogs
    template_external_list_url  : "lists/template_list.js",
    external_link_list_url      : "lists/link_list.js",
    external_image_list_url     : "lists/image_list.js",
    media_external_list_url     : "lists/media_list.js",

    // Style formats
    style_formats : [
      {title : 'Bold text', inline : 'b'},
      {title : 'Red text', inline  : 'span', styles : {color : '#ff0000'}},
      {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
      {title : 'Example 1', inline : 'span', classes : 'example1'},
      {title : 'Example 2', inline : 'span', classes : 'example2'},
      {title : 'Table styles'},
      {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
    ],

    // Replace values for the template plugin
    template_replace_values : {
      username : "Some User",
      staffid  : "991234"
    }
});


/* trigger when page is ready */
$(document).ready(function(){

  // open external links in new window
  $("a[rel=external]").click(function(){
    this.target = "_blank";
  });

  // smooth internal scroll
  $("a[href^='#']").click(function(event){
    event.preventDefault();
    $('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
  });

  // confirm restore clicks
  $(".restore").click(function(e) {
    var version = $(this).attr("id").split("-")[1];
    if (confirm('Are you sure you want to restore version ' + version + ' of the site?')) {
      // restore!
    } else {
      e.preventDefault();
    }
  });

  // preview popup links
  $(".preview").click(function(e) {
    e.preventDefault();
    window.open(this.href, 'child', 'height=400,width=500,scrollbars');
  });


  printFootnoteLinks.init("article","wrapper");

});

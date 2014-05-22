<link rel="stylesheet" type="text/css" media="screen" href="lib/jquery-ui/css/smoothness/jquery-ui-1.10.4.custom.css" />
<script src="js/jquery-1.11.0.min.js" type="text/javascript"></script>
<script type="text/javascript" src="lib/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript">
	(function ($) {
    var settings = {
        barheight: 38
    }    

    $.fn.scrollabletab = function (options) {
        var ops = $.extend(settings, options);

        var ul = this.children('ul').first();
        var ulHtmlOld = ul.html();
        var tabBarWidth = $(this).width()-60;
        ul.wrapInner('<div class="fixedContainer" style="height: ' + ops.barheight + 'px; width: ' + tabBarWidth + 'px; overflow: hidden; float: left;"><div class="moveableContainer" style="height: ' + ops.barheight + 'px; width: 5000px; position: relative; left: 0px;"></div></div>');
        ul.append('<div style="width: 20px; float: left; height: ' + (ops.barheight - 2) + 'px; margin-left: 5px; margin-right: 0;"></div>');
        var leftArrow = ul.children().last();
        leftArrow.button({ icons: { secondary: "ui-icon ui-icon-carat-1-w" } });
        leftArrow.children('.ui-icon-carat-1-w').first().css('left', '2px');        

        ul.append('<div style="width: 20px; float: left; height: ' + (ops.barheight - 2) + 'px; margin-left: 1px; margin-right: 0;"></div>');
        var rightArrow = ul.children().last();
        rightArrow.button({ icons: { secondary: "ui-icon ui-icon-carat-1-e" } });
        rightArrow.children('.ui-icon-carat-1-e').first().css('left', '2px');        

        var moveable = ul.find('.moveableContainer').first();
        leftArrow.click(function () {
            var offset = tabBarWidth / 6;
            var currentPosition = moveable.css('left').replace('px', '') / 1;

            if (currentPosition + offset >= 0) {
                moveable.stop().animate({ left: '0' }, 'slow');
            }
            else {
                moveable.stop().animate({ left: currentPosition + offset + 'px' }, 'slow');
            }
        });

        rightArrow.click(function () {
            var offset = tabBarWidth / 6;
            var currentPosition = moveable.css('left').replace('px', '') / 1;
            var tabsRealWidth = 0;
            ul.find('li').each(function (index, element) {
                tabsRealWidth += $(element).width();
                tabsRealWidth += ($(element).css('margin-right').replace('px', '') / 1);
            });

            tabsRealWidth *= -1;

            if (currentPosition - tabBarWidth > tabsRealWidth) {
                moveable.stop().animate({ left: currentPosition - offset + 'px' }, 'slow');
            }
        });
        return this;
    }; // end of functions

})(jQuery);
$(function () {
   $( "#tabs" ).tabs().scrollabletab(); 
});
</script>

<div id="tabs" style="width:900px">
<ul>
<li><a href="#fragment-1"><span>One dsads</span></a></li>
<li><a href="#fragment-2"><span>Two ewqewq</span></a></li>
<li><a href="#fragment-3"><span>Three okewoqkeq wqe</span></a></li>
<li><a href="#fragment-4"><span>Four okewoqkeq wqe ewqewq</span></a></li>
<li><a href="#fragment-5"><span>Five okewoqkeq wqe ewqewq</span></a></li>
</ul>
<div id="fragment-1">
<p>First tab is active by default:</p>
<pre><code>$( "#tabs" ).tabs(); </code></pre>
</div>
<div id="fragment-2">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
</div>
<div id="fragment-3">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
</div>
<div id="fragment-4">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
</div>
<div id="fragment-5">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
</div>
</div>

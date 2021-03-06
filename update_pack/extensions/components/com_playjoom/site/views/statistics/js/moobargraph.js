/*
---
description: Ajax graph plugin.

license: MIT-style

authors:
- Ivan Lazarevic

requires:
- core

provides: 
- mooBarGraph
...
*/

var mooBarGraph = new Class({
	
	Implements: [Options],
	
	options:{
		width: 400,
		height: 300,
		barSpace: 10,
		color: '#111111',
		colors: false,
		lbl: '',
		sort: false, // 'asc' or 'desc'
		prefix: '',
		postfix: '',
		legendWidth: 100,
		legend: false,
		legends: false,
		type: false, // or 'multi'
		showValues: true,
		showValuesColor: '#fff',
		title: false,
		realTime: false
	},
	
	initialize: function(options){
		
		this.setOptions(options);
		
		if(this.options.sort == 'asc') { 
			this.options.data.sort(this.sortNumberAsc);
		}
		if(this.options.sort == 'desc') {
			this.options.data.sort(this.sortNumberDesc);
		}
		
		this.unique		= 'mooBar_' + this.options.container.id;
		this.type		= (this.options.data[0][0] instanceof Array) ? 'multi' : 'simple';
		
		// we need only one info box on page
		if(!$('mooBar_infoBox')){
			this.infoBox = new Element('div',{
				id		: 'mooBar_infoBox',
				styles	: {
					position		: 'absolute',
					border			: '1px solid #999',
					padding			: '5px',
					display			: 'none',
					backgroundColor : 'black',
					color			: 'white',
					zIndex			: 50,
					opacity			: 0.8,
					'-webkit-box-shadow': 'rgba(0, 0, 0, 0.3) 1px 1px 1px',
					'-webkit-border-radius'	: '2px',
					'-moz-box-shadow': 'rgba(0, 0, 0, 0.3) 1px 1px 1px',
					'-moz-border-radius'	: '2px'					
				}
			});
	
			(this.infoBox).inject($(document.body));
		}
		
		// create title above graph
		if(this.options.title){
			var barHolderWrapper= new Element('div',{
				id		: this.options.container.id + '_wrapper',
				styles	: {
					width: this.options.width
				}
			});
			
			var barTitleHolder	= new Element('div', {
				html	: this.options.title,
				id		: this.unique+'_title',
				styles	: {
					textAlign : 'center'
				}
			});
			
			barHolderWrapper.wraps(this.options.container);
			barTitleHolder.inject(barHolderWrapper,'top');		
		}		
		// end title creating		
		
		this.createGraph(this.options.data);
		
	},

	// function for creating graph dynamicly
	draw: function(data){

		// ajax part
		if(!(data instanceof Array)){
			url 	= data;
			data 	= 0;
			var othis = this;
			var myRequest = new Request({
				method		: 'get', 
				url		: url,
				noCache		: true,
				onRequest	: function(){
							if(othis.options.realTime === false){
								$(othis.options.container.id).addClass('mooBar_ajaxBox').setProperty('html','');
							}
									//$(othis.options.container.id).setStyle('opacity','0.5');
						},
				onComplete 	: function(response){
							if(othis.options.realTime === true){
								$(othis.options.container.id).addClass('mooBar_ajaxBox').setProperty('html','');
							}
							data = eval('('+response+')');
							//data += othis.options.data;
							othis.type		= (data[0][0] instanceof Array) ? 'multi' : 'simple';
							$(othis.options.container.id).removeClass('mooBar_ajaxBox');
							othis.createGraph(data);

						}
			}).send();
				
		}
		// end of ajax part
		else {
			this.createGraph(data);
		}
		
		
	},
	
	createGraph: function(data){

		this.barCount 	= data.length; // number of bars
		this.legends 	= new Array;
		
		var legendWidth = this.options.legend ? this.options.legendWidth : 0;
		var barWidth 	= (this.options.width - legendWidth - this.options.barSpace*this.barCount) / this.barCount; // width of bar
		var barMax 		= this.max(data); // max value in data
		var barMin		= this.min(data);
		var barBottom	= 0; // in case of negative values bottom should be height of lowest value
		
		
		if(barMin < 0) {
			barMax 		+= Math.abs(barMin);
			barBottom 	= parseInt((this.options.height*Math.abs(barMin))/barMax);
		}
		
		$(this.options.container).setStyles({ 
			width		: this.options.width,
			height		: this.options.height,
			position 	: 'relative'
		}).set('html','');
		
		// render bars
		for(i=0; i<this.barCount; i++){
			
			barValue = data[i][0] instanceof Array ? this.sum(data[i][0]) : data[i][0];
			barHeight 		= (barValue == 0) ? '0px' : (this.options.height*Math.abs(barValue))/barMax;
			
			var barHolder	= new Element('div', { 
				id		: this.unique+'_'+i, 
				'class'	: this.unique,
				styles	: {
					height			: barHeight,
					width			: barWidth,
					float			: 'left',
					position		: 'absolute',
					left			: this.options.barSpace + i*(barWidth+this.options.barSpace),
					bottom			: (barValue < 0) ? barBottom-barHeight : barBottom, // for top or bottom position
					textAlign		: 'center'
				}
			});
					
			var barValueHolder 	= new Element('div', {
				id		: this.unique+'_'+i+'_value',
				'class'	: this.unique+'_value',				
				html	: this.options.prefix + '' + barValue + '' + this.options.postfix
			});

			var barLabelHolder	= new Element('div', {
				id		: this.unique+'_'+i+'_label',
				'class'	: this.unique+'_label',
				html	: data[i][1] ? data[i][1] : this.options.lbl
			});		
			
			var barBodyHolder	= new Element( data[i][3] ? 'a' : 'div', {
				id		: this.unique+'_'+i+'_body',
				'class'	: this.unique+'_body',
				href	: data[i][3],
				alt		: data[i][4],
				styles	: {
					backgroundColor : data[i][2] ? data[i][2] : 
											(this.options.colors) ? this.options.colors[i%this.options.colors.length] : this.options.color,
					height			: barHeight,
					width			: '100%',
					display			: 'none',
					fontSize		: (this.type == 'simple') ? 0 : '',
					textDecoration	: 'none'
				}
			});
			
			barBodyHolder.addEvent('mousemove', function(event){
				if(this.getProperty('href'))
					this.setStyles({ opacity: 0.5 });
				if(this.getProperty('alt')){
					$('mooBar_infoBox').setProperty('html', this.getProperty('alt'));
					$('mooBar_infoBox').setStyles({
						display : 'block',
						top 	: event.page.y + 5,
						left	: event.page.x + 5
					});	
				}
			});

			barBodyHolder.addEvent('mouseout', function(){
				this.setStyles({ opacity: 1 });
				$('mooBar_infoBox').setStyles({
					display : 'none'
				});				
			});			
			
			barValueHolder.inject(barHolder);	
			barHolder.inject(this.options.container);
			
			var lblvalHeight = 0;
			if(!this.options.legend || this.options.legends){
				barLabelHolder.inject(barHolder);
				lblvalHeight += parseInt($$('.mooBar_'+this.options.container.id+'_label').getStyle('height'));
			}
			lblvalHeight += parseInt($$('.mooBar_'+this.options.container.id+'_value').getStyle('height'));
			
			barBodyHolder.inject(barValueHolder,(barValue < 0) ? 'before' : 'after');

			barBodyHolder.setStyles({
				height	: barHeight - lblvalHeight,
				display: 'block'
			});
			
			var barBodyHolderHeight = parseInt(barBodyHolder.getStyle('height'));
			if (isNaN(barBodyHolderHeight)) {
				barBodyHolderHeight = 0;
			}
						
			barHolder.setStyles({
				height: lblvalHeight + barBodyHolderHeight,
				display: 'block'
			});
			
			/* part for creating stacked graph */
			if(this.type == 'multi'){
				for(k=0;k<data[i][0].length;k++){
					var barBodySub = new Element('span',{
						id		: this.unique+'_sub_'+k+'_'+i,
						html	: this.options.showValues ? this.options.prefix + '' + data[i][0][k] + this.options.postfix : '',
						styles	: {
							backgroundColor	: this.options.colors[k],
							height			: (barHeight-lblvalHeight)*data[i][0][k]/this.sum(data[i][0]),
							color			: this.options.showValuesColor,
							display			: 'block'
						}
					});
					barBodySub.inject(barBodyHolder,'top');
				}
			}	
			/* end of creating stacked graph */

			// create array of color,label pair for legend box for simple graph
			this.legends[i] = [ (data[i][2]) ? data[i][2] : 
									(this.options.colors) ? this.options.colors[i%this.options.colors.length] : this.options.color , data[i][1] ];
	
		}
		
		// create array of color,label pair for legend box for multi graph
		if(this.options.legends.length){ 
			this.legends = new Array;
			for (i=0;i<this.options.legends.length;i++){
				this.legends[i] = [ this.options.colors[i] ? this.options.colors[i] : this.options.color , this.options.legends[i] ];
			}
		}
		// end creating data for legend
		if(this.options.legend) {
			this.createLegend(); // create legend
		}

	},
	
	

	/* function for creating legend from array
	 * array(color,text)
	 */	
	createLegend : function(){

		var barLegend	= new Element('div', {
			id 		: this.unique+'_legend',
			'class' 	: 'mooBarLegend',
			styles 	: {
				width		: this.options.legendWidth,
				float		: 'right',
				textAlign 	: 'left'
			}
		});
		
		for (i=0;i<this.legends.length;i++){

			var barLegendRow = new Element('div', {
				id  	: this.unique+'_'+i+'_legend',
				styles 	: {
					overflow	: 'hidden',
					zoom		: 1
				}
			});
			
			var barLegendColor = new Element('div',{
				id		: this.unique+'_'+i+'_legend_color',
				styles	: {
					backgroundColor : this.legends[i][0],
					height			: 12,
					width			: 20,
					margin			: 3,
					fontSize		: 0,
					float			: 'left',
					'-webkit-box-shadow': 'rgba(0, 0, 0, 0.3) 1px 1px 1px',
					'-webkit-border-radius'	: '2px',
					'-moz-box-shadow': 'rgba(0, 0, 0, 0.3) 1px 1px 1px',
					'-moz-border-radius'	: '2px'						
				}
			});
			
			var barLegendText = new Element('div',{
				id		: this.unique+'_'+i+'_legend_text',
				html	: this.legends[i][1]
			});			
			
			barLegendColor.inject(barLegendRow);
			barLegendText.inject(barLegendRow);			
			barLegendRow.inject(barLegend);
		}
		
		barLegend.inject(this.options.container, 'bottom');
		
	},

	
	// count max value of array
	max: function(ar){
			var maxvalue = 0;
			for(var val in ar){
				value = ar[val][0];
				if(value instanceof Array) value = this.sum(value);	
				if (parseFloat(value) > parseFloat(maxvalue)) {
					maxvalue=value;
				}
			}	

		return maxvalue;	
	},
	
	min: function(ar){
		var minvalue = 0;
		for(var val in ar){
			value = ar[val][0];
			if (parseFloat(value) < parseFloat(minvalue)) {
				minvalue=value;
			}
		}
		return minvalue;
	},

	//sorting functions
	sortNumberAsc: function(a,b){
		if (a[0]<b[0]) return -1;
		if (a[0]>b[0]) return 1;
		return 0;
	},
	
	sortNumberDesc: function(a,b){
		if (a[0]>b[0]) return -1;
		if (a[0]<b[0]) return 1;
		return 0;
	},		
	
	// sum of array elements
	sum: function(ar){
		total = 0;
		for(j=0;j<ar.length;j++){
			total += ar[j];
		}
		
		return total.toFixed(2);
	}
	
});
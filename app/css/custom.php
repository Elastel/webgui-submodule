<?php header("Content-Type: text/css; charset=utf-8"); ?>
<?php
require_once '../../includes/functions.php';
$color = getColorOpt();
$model = getModel();
if ($model == "ElastBox400") {
  $model_font = 1.5;
  $model_top = 1.5;
} else {
  $model_font = 2.5;
  $model_top = 0.5;
}
?>

/*
Theme Name: RaspAP default
Author: @billz
Author URI: https://github.com/billz
Description: Default theme for RaspAP
License: GNU General Public License v3.0
*/

@import url('all.css');

:root {
  --raspap-theme-color: <?php echo $color; ?>;
}

body {
  color: #212529;
}

.h-underlined {
  border-bottom: 1px solid #e3e6f0;
  padding-bottom: 0.3rem;
}

.page-header {
  margin: 20px 0 20px;
}

.navbar-logo {
  margin-top: 0.5em;
  margin-left: 0.5em;
}

/* Small devices (portrait phones, up to 576px) */
@media (max-width: 576px) {
  .container-fluid, .card-body, .col-md-6 { padding-left: 0.5rem; padding-right: 0.5rem; }
  .card .card-header { padding: .75rem .5rem; font-size: 1.0rem; }
  .row { margin-left: 0rem; margin-right: 0rem; }
  .col-lg-12 { padding-right: 0.25rem; padding-left: 0.25rem; }
  .form-group.col-md-6 { margin-left: -0.5rem; }
  h4.mt-3 { margin-left: 0.5rem; }
}

.sidebar {
  background-color: #f8f9fc;
}

.sidebar-brand-text {
  text-transform: none;
  color: #212529;
  font-size: 2.0rem;
  font-weight: 500;
  font-family: Helvetica, Arial, sans-serif;
}

.sidebar .nav-item.active .nav-link {
  font-weight: 500;
}

.card .card-header, .modal-header {
  border-color: <?php echo $color; ?>;
  color: #fff;
  background-color: <?php echo $color; ?>;
}

.modal-header {
  border-radius: 0px;
}

.btn-primary {
  color: <?php echo $color; ?>;
  border-color: <?php echo $color; ?>;
  background-color: #fff;
}

.card-footer, .modal-footer {
  background-color: #f2f1f0;
}

.nav-item {
  font-size: 0.85rem;
  max-width: 100%;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-link {
  font-size: 1.0rem;
}

.nav-tabs a.nav-link {
  color: #6e707e;
}

a.nav-link.active {
  font-weight: bolder;
}

.sidebar .nav-item .nav-link {
  padding: 0.6rem 0.6rem 0.6rem 1.0rem;
  max-width: 100%;
}

.alert-success {
  background-color: #d4edda;
}

.btn-primary {
  background-color: #fff;
}

.btn-warning {
  color: #333;
}

.btn-primary:hover {
  background-color: <?php echo $color; ?>;
  border-color: <?php echo $color; ?>;
}

i.fa.fa-bars {
  color: #d1d3e2;
}

i.fa.fa-bars:hover{
  color: #6e707e;
}

.info-item {
  text-transform: uppercase;
  font-size: 0.7em;
  color: #858796;
}

.info-value {
  font-size: 0.7rem;
  margin-left: 0.7rem;
}

.info-item-xs {
  font-size: 0.7rem;
  margin-left: 0.3rem;
}

.info-item-wifi {
  width: 6rem;
  float: left;
}

.service-status {
  border-width: 0;
  align-items: center;
}

.service-status-up {
  color: #a1ec38;
}

.service-status-warn {
  color: #f6f044;
}

.service-status-down {
  color: #f80107;
  animation: flash 1s linear infinite;
}
@keyframes flash {
  50% {
    opacity: 0;
  }
}
 
.logoutput {
  width:100%;
  height: 20rem;
  border: 1px solid #d1d3e2;
  border-radius: .35rem;
}

pre.unstyled {
  border-width: 0;
  background-color: transparent;
  padding: 0;
}

.dhcp-static-leases {
  margin-top: 1em;
  margin-bottom: 1em;
}

.dhcp-static-lease-row {
  margin-top: 0.5em;
  margin-bottom: 0.5em;
}

.loading-spinner {
  background: url("../../app/img/loading-spinner.gif") no-repeat scroll center center transparent;
  min-height: 150px;
  width: 100%;
}

@media (min-width: 576px) {
  .card-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 50%;
    grid-gap: 1rem;
  }
}

.sidebar.toggled .nav-item .nav-link span {
  display: none;
} .sidebar .nav-item .nav-link i,
.sidebar .nav-item .nav-link span {
    font-size: 1.0rem;
}

.btn-warning:hover {
    color: #000;
}

.toggle-off.btn {
  padding-left: 1.2rem;
  font-size: 0.9rem!important;
}

.toggle-on.btn {
  font-size: 0.9rem!important;
}

canvas#divDBChartBandwidthhourly {
  height: 350px!important;
}

.chart-container {
  height: 150px;
  width: 200px;
}

.table {
  margin-bottom: 0rem;
}

.check-hidden {
  visibility: hidden;
}

.check-updated {
  opacity: 0;
  color: #90ee90;
}

.check-progress {
  color: #999;
}

.fa-check {
  color: #90ee90;
}

.fa-times {
  color: #ff4500;
}


.cbi-section{
    font-family: inherit;
    font-weight: 400;
    font-style: normal;
    line-height: normal;
    min-width: inherit;
    border: 0;
    border-radius: 0;
    background-color: #fff;
    box-shadow: 0 2px 2px 0 rgb(0 0 0 / 16%), 0 0 2px 0 rgb(0 0 0 / 12%);
    margin: 1rem 0 0;
    padding: 1rem;
}

.cbi-value {
  display: inline-block;
  width: 100%;
  padding: 0.35rem 1rem 0.2rem;
}
.cbi-tabmenu {
  border: thin solid #d4d4d4;
  border-bottom: 0;
  background-color: #d4d4d4;
}
.cbi-input-radio {
  margin-left:0rem;
}
.cbi-value-title {
  width: 100%;
  max-width: 15rem;
  min-width: 5rem;
  text-align:right;
  margin-right:0.5rem;
}
.cbi-input-text {
  width: 100%;
  min-width: 5rem;
  max-width: 20rem;
  height: calc(1.5em + 0.75rem);
  color: #6e707e;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #d1d3e2;
  border-radius: 0.35rem;
  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.cbi-input-checkbox {
  text-align:left;
}

.cbi-value-description {
    font-size: small;
    padding-top: 0.4rem;
    opacity: 0.5;
}

.cbi-page-actions {
  padding-top: 1rem;
  text-align: right;
}

.cbi-input-select {
  width: 100%;
  max-width: 20rem;
  min-width: 5rem;
  height: calc(1.5em + 0.75rem);
  color: #6e707e;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #d1d3e2;
  border-radius: 0.35rem;
  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.cbi-section-table .cbi-section-table-titles .cbi-section-table-cell {
	width: auto !important;
}

.cbi-section-table {
  max-width: auto;
  table-layout: fixed;
}

.cbi-section-table-titles {
  color:black;
}

.cbi-section-table-cell {
  line-height: 1.1;
  font-size:0.8rem;
  text-align:center;
}

.cbi-section-table-row {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .16), 0 0 2px 0 rgba(0, 0, 0, .12);
  text-align: center;
}

.cbi-section-create {
	display: flex;
  align-items: center;
  margin: 0.5rem -3px;
  flex-wrap: wrap;
  gap: 2rem;
}

.cbi-button-add {
  font-size: 1rem;
  margin-top: 2rem;
  text-transform: uppercase;
  border-radius: 0.2rem;
  font-weight: 400;
  color: #fff;
  border: thin solid <?php echo $color; ?>;
  background-color: <?php echo $color; ?>;
  max-width: 100%;
  padding: 0.5rem 1rem;
  box-sizing: border-box;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#popLayer,
#confLayer,
#forwards_popLayer,
#traffic_popLayer {
  display: none;
  background-color: #B3B3B3;
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 10;
  -moz-opacity: 0.8;
  opacity:.80;
  filter: alpha(opacity=80);
}

#popBox,
#confBox,
#forwards_popBox,
#traffic_popBox {
  display: none;
  background-color: #FFFFFF;
  z-index: 11;
  width: 85%;
  height: 85%;
  position:fixed;
  top:0rem;
  right:0;
  left:0;
  bottom:0;
  padding: 1em;
  margin: 3em auto;
}

#confBox {
  display: none;
  background-color: #FFFFFF;
  z-index: 11;
  width: 50%;
  height: 50%;
  position:fixed;
  top:0rem;
  right:0;
  left:0;
  bottom:0;
  padding: 1em;
  margin: 3em auto;
}

#popBox .close,
#forwards_popBox .close,
#traffic_popBox .close
{
  text-align: right;
  margin-right: 5px;
  background-color: #F8F8F8;
}

#popBox .close a,
#forwards_popBox .close a,
#traffic_popBox .close a {
  text-decoration: none;
  color: #2D2C3B;
}

.right,
.right::before {
  margin-top: 1rem;
	text-align: right !important;
}

/* button style */
.cbi-button,
.item::after {
	font-size: 1rem;
	display: inline-block;
	width: auto !important;
	padding: 0 .8rem;
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	transition: all .2s ease-in-out;
	text-align: center;
	vertical-align: middle;
	white-space: nowrap;
	text-decoration: none;
	text-transform: uppercase;
	color: rgba(0, 0, 0, .87);
	border: 0;
	border-radius: .2rem;
	background-color: #f0f0f0;
	background-image: none;
	-webkit-appearance: none;
	-ms-touch-action: manipulation;
	touch-action: manipulation;
}

.cbi-button-up,
.cbi-button-down {
	font-size: 1.2rem;
	display: inline-block;
	min-width: 0;
	padding: .2rem .3rem;
	color: transparent !important;
	background: url(./icons/arrow.svg) no-repeat center;
	background-size: 12px 20px;
}

.cbi-button-up {
	transform: scaleY(-1);
}

.cbi-button-positive {
	font-weight: normal;
	color: #fff;
	border: thin solid <?php echo $color; ?>;
	background-color: <?php echo $color; ?>;
}

.cbi-file-lable{
  position: relative;
}
.cbi-file {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
}
.cbi-file-btn{
  margin-right: 5px;
}

.cbi-model {
  font-size:<?php echo $model_font ?>rem;
  padding-top:<?php echo $model_top ?>rem;
}

.load {
  display: none;
  border: 4px solid #f3f3f3;
  border-radius: 50%;
  border-top: 4px solid <?php echo $color; ?>;
  border-bottom: 4px solid <?php echo $color; ?>;
  width: 30px;
  height: 30px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}  
@-webkit-keyframes spin {
  0% {-webkit-transform: rotate(0deg);}
  100% {-webkit-transform: rotate(360deg);}
}

@keyframes spin {
  0% {transform: rotate(0deg);}
  100% {transform: rotate(360deg);}
}

.table-label-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  gap: 1rem;
  flex-wrap: wrap;
}

.table-label-key{
  text-align:right; 
  width:9rem;
  font-weight:bold;
  font-size:1.2rem;
  //border:1px solid;
  //background-color:red;
}

.table-label-value{
  text-align:center; 
  width:8rem; 
  border:1px solid;
  color:red;
  border-color:black;
  font-size:1.2rem;
  //background-color:red;
}

.info-label{
  width: 30rem;
  font-size: 0.8em;
  color: #858796;
}

.conf-btn{
  font-size: 1rem;
	display: inline-block;
	width: auto !important;
	padding: 0 .8rem;
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	transition: all .2s ease-in-out;
	text-align: center;
	vertical-align: middle;
	white-space: nowrap;
	text-decoration: none;
	text-transform: uppercase;
	color: rgba(0, 0, 0, .87);
	border: 0;
	border-radius: .2rem;
	background-color: #f0f0f0;
	background-image: none;
	-webkit-appearance: none;
	-ms-touch-action: manipulation;
	touch-action: manipulation;
}
.conf-btn:hover {
  background-color: #E81123;
  border: 0;
}

.custom-fullscreen {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    max-width: none;
}

.custom-fullscreen .modal-content {
    height: 100%;
    // background: url('app/img/bg.png') no-repeat center center;
    background-size: cover !important;
    border: none;
}

#page_datadisplay {
  max-height: 35rem;
  overflow-y: auto;
  height: auto;
}

.dropdown {
    position: relative;
    display: inline-block;
    width: 200px;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    width: 20rem;
    max-height: 150px;
    overflow-y: auto;
    left: 18.8rem
}

.dropdown-content div {
    padding: 10px;
    cursor: pointer;
}

.dropdown-content div:hover {
    background-color: #f1f1f1;
}

.show {
    display: block;
}
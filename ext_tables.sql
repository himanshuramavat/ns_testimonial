CREATE TABLE tx_nstestimonial_domain_model_testimonial (
	title varchar(255) NOT NULL DEFAULT '',
	description text,
	name varchar(255) NOT NULL DEFAULT '',
	position varchar(255) NOT NULL DEFAULT '',
	rating int(11) NOT NULL DEFAULT '0',
	image int(11) unsigned NOT NULL DEFAULT '0'
);

#
# Table structure for table 'tx_nstestimonial_domain_model_apidata'
#
CREATE TABLE tx_nstestimonial_domain_model_apidata (
   id int(11) NOT NULL auto_increment,
   extension_key varchar(255) DEFAULT '',
   right_sidebar_html text,
   premuim_extension_html text,
   support_html text,
   footer_html text,
   last_update date,
   PRIMARY KEY (id)
);

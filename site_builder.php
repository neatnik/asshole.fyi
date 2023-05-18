<?php

$languages = file('site_languages.md', FILE_IGNORE_NEW_LINES);
$redirects = null;

$rtl = array('ar', 'az', 'dv', 'he', 'ku', 'fa', 'ur');

foreach($languages as $line) {
	$line = explode('/', str_replace(array('* ', '[', ']', '(', ')'), '', $line));
	$language = $line[0];
	$language_code = $line[1];
	$translations[] = $language_code;
	$language_nav[$language_code] = $language;
}

foreach($translations as $translation) {
	$template = file_get_contents('site_template.html');
	$file = file('site_translations/'.$translation.'.md', FILE_IGNORE_NEW_LINES);
	$code_of_conduct = trim(str_replace('#', '', $file[0]));
	$code_of_conduct_heading = trim(str_replace('##', '', $file[2]));
	$code_of_conduct_intro = trim($file[4]);
	$asked_to_visit_question = trim(str_replace('##', '', $file[6]));
	$asked_to_visit_suggestion = trim($file[8]);
	$footer = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="\2">\1</a>', trim($file[12]));
	
	$language_list = '<ul>';
	foreach($language_nav as $language_code => $language) {
		if($language_code == $translation) $active = 'class="active" ';
		else $active = null;
		$language_list .= "\n\t\t\t".'<li><a '.$active.'href="/'.$language_code.'/">'.$language.'</a></li>';
	}
	$language_list .= "\n\t\t".'</ul>';
	
	// rtl support
	if(in_array($translation, $rtl)) {
		$direction = 'rtl';
	}
	else {
		$direction = 'auto';
	}
	
	$template = str_replace('{language}', $translation, $template);
	$template = str_replace('{direction}', $direction, $template);
	$template = str_replace('{languages}', $language_list, $template);
	$template = str_replace('{code_of_conduct}', $code_of_conduct, $template);
	$template = str_replace('{code_of_conduct_heading}', $code_of_conduct_heading, $template);
	$template = str_replace('{code_of_conduct_intro}', $code_of_conduct_intro, $template);
	$template = str_replace('{asked_to_visit_question}', $asked_to_visit_question, $template);
	$template = str_replace('{asked_to_visit_suggestion}', $asked_to_visit_suggestion, $template);
	$template = str_replace('{footer}', $footer, $template);
	
	if(!file_exists('site')) {
		mkdir('site');
		chmod('site', 0777);
	}
	if(!file_exists('site/'.$translation)) {
		mkdir('site/'.$translation);
		chmod('site/'.$translation, 0777);
	}
	file_put_contents('site/'.$translation.'/index.html', $template);
	chmod('site/'.$translation.'/index.html', 0777);
	if($translation == 'en') {
		file_put_contents('site/index.html', str_replace('"../style.css"', '"style.css"', $template));
		chmod('site/index.html', 0777);
	}
	$redirects .= "[[redirects]]\nfrom = \"https://asshole.fyi\"\nto = \"https://asshole.fyi/$translation/\"\nstatus = 302\nforce = true\nconditions = {Language = [\"$translation\"]}\n\n";
}

file_put_contents('site/netlify.toml', $redirects);

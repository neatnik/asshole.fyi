<?php

$languages = file('site_languages.md', FILE_IGNORE_NEW_LINES);

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
	$title = trim(str_replace('#', '', $file[0]));
	$code_of_conduct_heading = trim(str_replace('##', '', $file[2]));
	$code_of_conduct_intro = trim($file[4]);
	$code_of_conduct = trim($file[6]);
	$asked_to_visit_question = trim(str_replace('##', '', $file[8]));
	$asked_to_visit_suggestion = trim($file[10]);
	$footer = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="\2">\1</a>', trim($file[14]));
	
	$language_list = '<ul>';
	foreach($language_nav as $language_code => $language) {
		if($language_code == $translation) $active = 'class="active" ';
		else $active = null;
		$language_list .= "\n\t\t".'<li><a '.$active.'href="/'.$language_code.'/">'.$language.'</a></li>';
	}
	$language_list .= "\n\t".'</ul>';
	
	$template = str_replace('{language}', $translation, $template);
	$template = str_replace('{languages}', $language_list, $template);
	$template = str_replace('{title}', $title, $template);
	$template = str_replace('{code_of_conduct_heading}', $code_of_conduct_heading, $template);
	$template = str_replace('{code_of_conduct_intro}', $code_of_conduct_intro, $template);
	$template = str_replace('{code_of_conduct}', $code_of_conduct, $template);
	$template = str_replace('{asked_to_visit_question}', $asked_to_visit_question, $template);
	$template = str_replace('{asked_to_visit_suggestion}', $asked_to_visit_suggestion, $template);
	$template = str_replace('{footer}', $footer, $template);
	
	if(!file_exists('site/'.$translation)) {
		mkdir('site/'.$translation);
		chmod('site/'.$translation, 0777); 
	}
	file_put_contents('site/'.$translation.'/index.html', $template);
	chmod('site/'.$translation.'/index.html', 0777); 
	if($translation == 'en') {
		file_put_contents('site/index.html', $template);
		chmod('site/index.html', 0777); 
	}
}
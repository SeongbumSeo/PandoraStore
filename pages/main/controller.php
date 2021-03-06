<?php
require_once("libraries/functions/template.php");
require_once("models/orbit.php");
require_once("models/categories.php");
require_once("models/contents.php");

/* 인자 전처리 */ {
    $category = $_GET["category"];
    preg_match("/G([0-9]+)/", $category, $genre);
    preg_match("/P([0-9]+)/", $category, $platform);
    preg_match("/T([0-9]+)/", $category, $tag);
    $genre = (count($genre) < 2) ? null : $genre[1];
    $platform = (count($platform) < 2) ? null : $platform[1];
    $tag = (count($tag) < 2) ? null : $tag[1];

    $filtered = (int)(isset($genre) || isset($platform) || isset($tag));

    $search = $_GET["search"];
}

/* 데이터 로드 */ {
    $orbit_model = new Orbit();
    $categories_model = new Categories();
    $contents_model = new Contents($genre, $platform, $tag, null, $search);

    $orbit_data = $orbit_model->getData();
    $category_names = $categories_model->getNames();
    $category_tags = $categories_model->getTags();
    $contents_data = $contents_model->getContents();

    $genre_name = &$category_names["Genre"][$genre];
    $platform_name = &$category_names["Platform"][$platform];
    $tag_name = &$category_names["Tag"][$tag];
    if (isset($search)) {
        $category_name = "검색 결과 - {$search}";
        $title_text = "{$search} - 판도라스토어 검색";
    } else if(isset($genre_name) && isset($platform_name)) {
        $category_name = "{$genre_name} / {$platform_name}";
        $title_text = "{$genre_name}, {$platform_name} - 판도라스토어";
    } else if(isset($genre_name)) {
        $category_name = $genre_name;
        $title_text = "{$genre_name} - 판도라스토어";
    } else if(isset($platform_name)) {
        $category_name = $platform_name;
        $title_text = "{$platform_name} - 판도라스토어";
    } else if(isset($tag_name)) {
        $category_name = $tag_name;
        $category_description = "";
        foreach ($category_tags as $category_tag) {
            if ($category_tag["ID"] == $tag) {
                $category_description = $category_tag["Description"];
                break;
            }
        }
        $title_text = "{$tag_name} - 판도라스토어";
    } else {
        $category_name = null;
        $title_text = "판도라스토어";
    }
}

/* 뷰 로드 */ {
    $template = new Template($title_text);
    $template->setAttribute("orbit", $orbit_data);
    $template->setAttribute("contents", $contents_data);
    $template->setAttribute("category_name", $category_name);
    $template->setAttribute("category_description", $category_description);
    $template->setAttribute("tags", $category_tags);
    $template->setAttribute("filtered", $filtered);
    $template->setAttribute("search", addslashes($search));

    if (count($orbit_data) === 0) { // Orbit 데이터가 존재하지 않을 시 비활성화
        $template->disableArea("topOrbit");
    }
    
    $template->loadView("main");
}
?>

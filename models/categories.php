<?php
class Categories {
    private $mysqli;
    private $table;
    private $names;
    private $tags;

    public function __construct() {
        $config_db = parse_ini_file("configs/database.ini");

        $this->mysqli = mysqli_connect($config_db["host"], $config_db["user"], $config_db["password"], $config_db["database"]);
        $this->table = $config_db["table"];

        if ($this->mysqli) {
            $this->loadNames();
            $this->loadTags();
        }
    }

    public function loadNames() {
        $sql = "
            SELECT *, 'Genre' AS Type
            FROM {$this->table["genres"]}
            UNION
            SELECT *, 'Platform' AS Type
            FROM {$this->table["platforms"]}
            UNION
            SELECT *, 'Tag' AS Type
            FROM {$this->table["tags"]}";

        $this->names = array();
        if($result = $this->mysqli->query($sql)) {
            for($i = 0; $i < $result->num_rows; $i++) {
                $data = $result->fetch_assoc();
                $this->names[$data["Type"]][$data["ID"]] = $data["Name"];
            }
            $result->free();
        }
        return $this->names;
    }

    public function loadTags() {
        $sql = "
            SELECT ID, Name 
            FROM {$this->table["tags"]}
            ORDER BY ID DESC";

        $this->tags = array();
        if($result = $this->mysqli->query($sql)) {
            for($i = 0; $i < $result->num_rows; $i++) {
                $this->tags[$i] = $result->fetch_assoc();
            }
            $result->free();
        }
        return $this->tags;
    }

    public function getNames() {
        return $this->names;
    }

    public function getTags() {
        return $this->tags;
    }

    public function parseArray($origin, &$data, $type) {
        $data["{$type}sID"] = explode(',', $origin["{$type}s"]);
    }

    public function parseName($origin, &$data, $type) {
        $result = array();
        foreach(explode(',', $origin["{$type}s"]) as $id)
            array_push($result, $this->names[$type][$id]);
        $data["{$type}s"] = $result;
    }
}
?>

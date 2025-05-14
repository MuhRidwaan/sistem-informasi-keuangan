<?php
class Pagination {

    private $koneksi;
    private $table;
    private $perPage;
    private $where;
    private $orderBy;

    public $page;
    public $total;
    public $pages;
    public $data = [];

    function __construct($koneksi, $table, $perPage = 10, $where = "", $orderBy = "") {
        $this->koneksi = $koneksi;
        $this->table = $table;
        $this->perPage = $perPage;
        $this->where = $where;
        $this->orderBy = $orderBy;
        $this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    }

    public function getData() {
        $mulai = ($this->page > 1) ? ($this->page * $this->perPage) - $this->perPage : 0;
        
        $whereClause = $this->where != "" ? "WHERE " . $this->where : "";
        $orderClause = $this->orderBy != "" ? "ORDER BY " . $this->orderBy : "";

        $result = mysqli_query($this->koneksi, "SELECT * FROM {$this->table} $whereClause");
        $this->total = mysqli_num_rows($result);
        $this->pages = ceil($this->total / $this->perPage);

        $query = mysqli_query($this->koneksi, 
            "SELECT * FROM {$this->table} $whereClause $orderClause LIMIT $mulai, $this->perPage"
        );

        while($row = mysqli_fetch_assoc($query)) {
            $this->data[] = $row;
        }

        return $this->data;
    }

    public function renderLinks($baseUrl = '?') {
        $html = '<nav><ul class="pagination">';
        for ($i = 1; $i <= $this->pages; $i++) {
            $active = $i == $this->page ? 'active' : '';
            $html .= "<li class='page-item $active'><a class='page-link' href='{$baseUrl}page={$i}'>{$i}</a></li>";
        }
        $html .= '</ul></nav>';
        return $html;
    }

}
?>

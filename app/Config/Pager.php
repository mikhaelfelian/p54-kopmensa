<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pager extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Templates
     * --------------------------------------------------------------------------
     *
     * Pagination links are rendered out using views to configure their
     * appearance. This array contains aliases and the view names to
     * use when rendering the links.
     *
     * Within each view, the Pager object will be available as $pager,
     * and the desired group as $pagerGroup;
     *
     * @var array<string, string>
     */
    public array $templates = [
        'default_full'   => 'CodeIgniter\Pager\Views\default_full',
        'default_simple' => 'CodeIgniter\Pager\Views\default_simple',
        'default_head'   => 'CodeIgniter\Pager\Views\default_head',
    ];

    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Get database connection
        $db = \Config\Database::connect();

        // Get theme path from database
        $theme = $db->table('tbl_pengaturan_theme')
                   ->where('status', 1)
                   ->get()
                   ->getRow();

        // Get pagination limit from pengaturan
        $pengaturan = $db->table('tbl_pengaturan')
                        ->where('id', 1)
                        ->get()
                        ->getRow();

        // Set pagination template path based on active theme
        $themePath = $theme ? $theme->path : 'admin-lte-3';
        $this->templates['adminlte_pagination'] = $themePath . '/layout/pagers/adminlte_pagination';

        // Set per page limit from pengaturan
        if ($pengaturan && isset($pengaturan->pagination_limit)) {
            $this->perPage = (int) $pengaturan->pagination_limit;
        }
    }

    /**
     * --------------------------------------------------------------------------
     * Items Per Page
     * --------------------------------------------------------------------------
     *
     * The default number of results shown in a single page.
     */
    public int $perPage = 20;
}

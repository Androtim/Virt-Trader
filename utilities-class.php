<?php
// includes/utilities.php

// Protection contre l'accès direct au fichier
if (!defined('ACCESS_GRANTED')) {
    header("HTTP/1.1 403 Forbidden");
    exit('Accès direct interdit');
}

class Utilities {
    // Formater un nombre en monnaie
    public static function formatMoney($amount) {
        return number_format($amount, 2, ',', ' ') . ' €';
    }
    
    // Obtenir le nom du mois en français
    public static function getMonthName($monthNumber) {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        
        return $months[$monthNumber] ?? '';
    }
    
    // Formater une date
    public static function formatDate($date, $format = 'd/m/Y H:i') {
        return date($format, strtotime($date));
    }
    
    // Générer une couleur aléatoire en HEX
    public static function getRandomColor() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
    
    // Obtenir l'indicateur de performance avec classe et icône
    public static function getPerformanceIndicator($value) {
        if ($value > 0) {
            return '<span class="text-success">↑ ' . number_format(abs($value), 2) . '%</span>';
        } elseif ($value < 0) {
            return '<span class="text-danger">↓ ' . number_format(abs($value), 2) . '%</span>';
        } else {
            return '<span class="text-muted">0.00%</span>';
        }
    }
    
    // Générer un tableau de couleurs pour les graphiques
    public static function getChartColors($count) {
        $baseColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#858796', '#6f42c1', '#5a5c69', '#fd7e14', '#20c997'
        ];
        
        $hoverColors = [
            '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b',
            '#757575', '#5d36a4', '#4e4f5c', '#ec6c0e', '#199d76'
        ];
        
        $result = [
            'background' => [],
            'hover' => []
        ];
        
        for ($i = 0; $i < $count; $i++) {
            $index = $i % count($baseColors);
            $result['background'][] = $baseColors[$index];
            $result['hover'][] = $hoverColors[$index];
        }
        
        return $result;
    }
    
    // Pagination pour les résultats
    public static function paginate($items, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $totalPages = ceil(count($items) / $perPage);
        
        $paginatedItems = array_slice($items, $offset, $perPage);
        
        return [
            'items' => $paginatedItems,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => count($items),
            'totalPages' => $totalPages,
            'hasMorePages' => $page < $totalPages,
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page < $totalPages ? $page + 1 : null,
        ];
    }
}

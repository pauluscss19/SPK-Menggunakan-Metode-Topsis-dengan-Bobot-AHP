<?php
class AHP {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Matriks Perbandingan Berpasangan (Skala Saaty 1-9)
    public function getMatriksPerbandingan() {
        // C1=Harga, C2=Jarak, C3=Fasilitas, C4=Keamanan, C5=Kebersihan
        return [
            [1,    2,    3,    3,    5],    // C1
            [1/2,  1,    2,    2,    3],    // C2
            [1/3,  1/2,  1,    2,    2],    // C3
            [1/3,  1/2,  1/2,  1,    2],    // C4
            [1/5,  1/3,  1/2,  1/2,  1]     // C5
        ];
    }
    
    public function hitungBobot() {
        $matriks = $this->getMatriksPerbandingan();
        $n = count($matriks);
        
        // Hitung jumlah kolom
        $jumlahKolom = array_fill(0, $n, 0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $jumlahKolom[$j] += $matriks[$i][$j];
            }
        }
        
        // Normalisasi matriks
        $matriksNormalisasi = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matriksNormalisasi[$i][$j] = $matriks[$i][$j] / $jumlahKolom[$j];
            }
        }
        
        // Hitung rata-rata baris (bobot)
        $bobot = [];
        for ($i = 0; $i < $n; $i++) {
            $jumlah = 0;
            for ($j = 0; $j < $n; $j++) {
                $jumlah += $matriksNormalisasi[$i][$j];
            }
            $bobot[$i] = $jumlah / $n;
        }
        
        return $bobot;
    }
    
    public function hitungConsistencyRatio() {
        $matriks = $this->getMatriksPerbandingan();
        $bobot = $this->hitungBobot();
        $n = count($matriks);
        
        // Hitung λmax
        $weightedSum = array_fill(0, $n, 0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedSum[$i] += $matriks[$i][$j] * $bobot[$j];
            }
        }
        
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $weightedSum[$i] / $bobot[$i];
        }
        $lambdaMax = $lambdaMax / $n;
        
        // Hitung CI
        $CI = ($lambdaMax - $n) / ($n - 1);
        
        // Random Index untuk n=5
        $RI = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45];
        
        // Hitung CR
        $CR = $CI / $RI[$n];
        
        return [
            'lambda_max' => $lambdaMax,
            'CI' => $CI,
            'CR' => $CR,
            'konsisten' => ($CR < 0.1)
        ];
    }
}
?>

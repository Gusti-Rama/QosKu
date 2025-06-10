<!-- <?php
//uncomment includenya kalo mau pake
// include 'connect.php';

// DELETE THIS FILE AFTER USE!

$accounts = [
    [
        'username' => 'admin',
        'password' => 'inipassword',
        'namaAdmin' => 'Aku Admin',
        'peran' => 'admin'
    ],
    [
        'username' => 'owner',
        'password' => 'inipassword',
        'namaAdmin' => 'Aku Owner',
        'peran' => 'owner'
    ]
];

try {
    $connect->begin_transaction();
    
    foreach ($accounts as $account) {
        $hashedPassword = password_hash($account['password'], PASSWORD_DEFAULT);
        
        $stmt = $connect->prepare("INSERT INTO admin (username, password, namaAdmin, peran) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", 
            $account['username'],
            $hashedPassword,
            $account['namaAdmin'],
            $account['peran']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create account: " . $account['username']);
        }
    }
    
    $connect->commit();
    echo "Admin accounts created successfully! DELETE THIS FILE NOW!";
    
} catch (Exception $e) {
    $connect->rollback();
    die("Error: " . $e->getMessage());
}
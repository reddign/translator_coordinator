<?php
/*
 * Unit test for isValidEtownEmail() in api/users/user_functions.php
 * Covers REQ1: only Elizabethtown College students can register.
 * Run from the project folder:  php tests/EtownEmailTest.php
 */

require_once __DIR__ . "/../api/users/user_functions.php";

$passed = 0;
$failed = 0;

function check(string $name, bool $expected, bool $actual): void {
    global $passed, $failed;
    if ($expected === $actual) {
        echo "PASS  $name\n";
        $passed++;
    } else {
        echo "FAIL  $name (expected " . var_export($expected, true)
           . ", got " . var_export($actual, true) . ")\n";
        $failed++;
    }
}

// Should be accepted
check("Normal Etown email",        true,  isValidEtownEmail("john@etown.edu"));
check("Uppercase Etown email",     true,  isValidEtownEmail("JOHN@ETOWN.EDU"));
check("Spaces around the email",   true,  isValidEtownEmail("  john@etown.edu  "));
check("Dots in the name",          true,  isValidEtownEmail("john.smith@etown.edu"));

// Should be rejected
check("Gmail address",             false, isValidEtownEmail("john@gmail.com"));
check("Etown inside another domain", false, isValidEtownEmail("john@etown.edu.fake.com"));
check("Look-alike domain",         false, isValidEtownEmail("john@fake-etown.edu"));
check("No name before @",          false, isValidEtownEmail("@etown.edu"));
check("Two @ signs",               false, isValidEtownEmail("a@gmail.com@etown.edu"));
check("Script tag as the name",    false, isValidEtownEmail("<script>@etown.edu"));
check("Empty string",              false, isValidEtownEmail(""));

$total = $passed + $failed;
echo "\n";
if ($failed === 0) {
    echo "ALL $total TESTS PASSED\n";
    exit(0);
}
echo "$failed OF $total TESTS FAILED\n";
exit(1);
<?php
/**
 * SchoolContext - Multi-School Data Isolation Helper
 *
 * This class ensures that all database queries are automatically
 * scoped to the current user's school_id, preventing cross-school
 * data access.
 */

class SchoolContext
{
    private static $currentSchoolId = null;
    private static $isSuperadmin = false;

    /**
     * Initialize the school context from session
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is superadmin (no school restriction)
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin') {
            self::$isSuperadmin = true;
            self::$currentSchoolId = null; // Superadmin sees all
        } elseif (isset($_SESSION['school_id'])) {
            self::$currentSchoolId = (int)$_SESSION['school_id'];
        }
    }

    /**
     * Get the current school ID
     * Returns null if superadmin (no restriction)
     */
    public static function getSchoolId(): ?int
    {
        if (self::$currentSchoolId === null && !self::$isSuperadmin) {
            self::init();
        }
        return self::$currentSchoolId;
    }

    /**
     * Check if current user is superadmin
     */
    public static function isSuperadmin(): bool
    {
        if (self::$currentSchoolId === null && !self::$isSuperadmin) {
            self::init();
        }
        return self::$isSuperadmin;
    }

    /**
     * Set the school context manually (for superadmin viewing specific school)
     */
    public static function setSchoolId(int $schoolId): void
    {
        self::$currentSchoolId = $schoolId;
    }

    /**
     * Clear the school context (reset to superadmin view)
     */
    public static function clearSchoolId(): void
    {
        if (self::$isSuperadmin) {
            self::$currentSchoolId = null;
        }
    }

    /**
     * Add school_id condition to a WHERE clause
     * Returns empty string for superadmin (sees all data)
     *
     * @param string $tableAlias Optional table alias for JOIN queries
     * @param bool $isFirstCondition If true, returns "WHERE", else "AND"
     */
    public static function whereClause(string $tableAlias = '', bool $isFirstCondition = false): string
    {
        if (self::isSuperadmin() && self::$currentSchoolId === null) {
            return ''; // Superadmin sees all
        }

        $prefix = $isFirstCondition ? 'WHERE' : 'AND';
        $column = $tableAlias ? "{$tableAlias}.school_id" : 'school_id';

        return " {$prefix} {$column} = " . self::getSchoolId() . " ";
    }

    /**
     * Get school_id for INSERT operations
     * Throws exception if no school context set
     */
    public static function requireSchoolId(): int
    {
        $schoolId = self::getSchoolId();
        if ($schoolId === null) {
            throw new Exception('No school context set. Cannot perform operation.');
        }
        return $schoolId;
    }

    /**
     * Validate that user can access a specific school
     */
    public static function canAccessSchool(int $schoolId): bool
    {
        // Superadmin can access any school
        if (self::isSuperadmin()) {
            return true;
        }

        // Regular users can only access their own school
        return self::getSchoolId() === $schoolId;
    }

    /**
     * Get school details for current context
     */
    public static function getCurrentSchool(): ?array
    {
        $schoolId = self::getSchoolId();
        if ($schoolId === null) {
            return null;
        }

        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT * FROM schools WHERE id = ?",
            [$schoolId]
        );
    }

    /**
     * Ensure only superadmin can perform action
     */
    public static function requireSuperadmin(): void
    {
        if (!self::isSuperadmin()) {
            http_response_code(403);
            die('Access denied. Superadmin privileges required.');
        }
    }
}

// Initialize on include
SchoolContext::init();

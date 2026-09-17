<?php

// Establishing types for proficiency levels
enum ProficiencyLevel: string {
    case BEGINNER = 'Beginner';
    case INTERMEDIATE = 'Intermediate';
    case EXPERT = 'Expert';
    case FLUENT = 'Fluent';
}

class UserLanguageProficiency {
    public function __construct(
        public int $userId,
        public int $languageId,
        public ProficiencyLevel $proficiency
    ) {}
}

class LanguageProficiencyService {
    /**
     * Updates or inserts a user's proficiency level in database.
     */
    public function updateUserProficiency(int $userId, int $languageId, ProficiencyLevel $proficiency): bool {
        // TODO: PDO/ORM query to UPSERT user_languages SET proficiency = $proficiency->value
        // WHERE user_id = $userId AND language_id = $languageId
        throw new Exception("Not implemented");
    }

    /**
     * Retrieves all languages and proficiency levels for a specific user to allow for other users to see.
     */
    public function getUserProficiencies(int $userId): array {
        // TODO: SELECT language_name, proficiency FROM user_languages WHERE user_id = $userId
        throw new Exception("Not implemented");
    }
}

class LanguageProficiencyController {
    private LanguageProficiencyService $service;

    public function __construct() {
        $this->service = new LanguageProficiencyService();
    }

    /**
     * Endpoint: POST /api/user/language-proficiency
     */
    public function handleUpdate(): void {
        // TODO: Read JSON input payload ($userId, $languageId, $rawLevel)
        // TODO: Validate $rawLevel using ProficiencyLevel::tryFrom($rawLevel)
        // TODO: Call $this->service->updateUserProficiency()
        // TODO: Return HTTP 200 JSON success response or HTTP 400 validation error
    }

    /**
     * Endpoint: GET /api/user/{id}/language-proficiency
     */
    public function handleGet(int $userId): void {
        // TODO: Call $this->service->getUserProficiencies($userId)
        // TODO: Return HTTP 200 JSON payload containing language list & levels
    }
}

?>
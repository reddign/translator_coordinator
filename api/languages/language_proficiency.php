<?php

// --- Establishing Types ---
export type ProficiencyLevel = 'Beginner' | 'Intermediate' | 'Expert' | 'Fluent';

export interface LanguageProficiency{
    userID: string;
    languageID: string;
    proficiency: ProficiencyLevel;
}

?>
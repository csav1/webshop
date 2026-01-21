<?php

namespace Core;

/**
 * Validator - Formular-Validierung
 * 
 * Features:
 * - Verschiedene Validierungsregeln
 * - Fehlermeldungen auf Deutsch
 * - Einfache API
 */
class Validator
{
    private array $data = [];
    private array $rules = [];
    private array $errors = [];
    private array $customMessages = [];

    private array $defaultMessages = [
        'required' => 'Das Feld :field ist erforderlich.',
        'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
        'min' => 'Das Feld :field muss mindestens :param Zeichen lang sein.',
        'max' => 'Das Feld :field darf maximal :param Zeichen lang sein.',
        'numeric' => 'Das Feld :field muss eine Zahl sein.',
        'integer' => 'Das Feld :field muss eine Ganzzahl sein.',
        'alpha' => 'Das Feld :field darf nur Buchstaben enthalten.',
        'alphanumeric' => 'Das Feld :field darf nur Buchstaben und Zahlen enthalten.',
        'confirmed' => 'Die Bestätigung für :field stimmt nicht überein.',
        'unique' => 'Dieser Wert für :field existiert bereits.',
        'exists' => 'Der ausgewählte Wert für :field ist ungültig.',
        'url' => 'Bitte geben Sie eine gültige URL ein.',
        'date' => 'Bitte geben Sie ein gültiges Datum ein.',
        'in' => 'Der ausgewählte Wert für :field ist ungültig.',
        'regex' => 'Das Format für :field ist ungültig.',
        'password' => 'Das Passwort muss mindestens 8 Zeichen, einen Großbuchstaben und eine Zahl enthalten.',
        'decimal' => 'Das Feld :field muss eine gültige Dezimalzahl sein.',
    ];

    /**
     * Neuen Validator erstellen
     */
    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
    }

    /**
     * Statische Fabrik-Methode
     */
    public static function make(array $data, array $rules, array $customMessages = []): self
    {
        return new self($data, $rules, $customMessages);
    }

    /**
     * Validierung durchführen
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->getValue($field);

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Wert aus Daten abrufen (unterstützt Dot-Notation)
     */
    private function getValue(string $field): mixed
    {
        $keys = explode('.', $field);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Regel anwenden
     */
    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $param = null;

        if (str_contains($rule, ':')) {
            [$rule, $param] = explode(':', $rule, 2);
        }

        $method = 'validate' . ucfirst($rule);

        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $param)) {
                $this->addError($field, $rule, $param);
            }
        }
    }

    /**
     * Fehler hinzufügen
     */
    private function addError(string $field, string $rule, ?string $param = null): void
    {
        $message = $this->customMessages["{$field}.{$rule}"]
            ?? $this->customMessages[$field]
            ?? $this->defaultMessages[$rule]
            ?? 'Das Feld :field ist ungültig.';

        $fieldLabel = ucfirst(str_replace('_', ' ', $field));
        $message = str_replace(':field', $fieldLabel, $message);
        $message = str_replace(':param', $param ?? '', $message);

        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }

    /**
     * Fehler abrufen
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Erste Fehlermeldung pro Feld
     */
    public function firstErrors(): array
    {
        return array_map(fn($errors) => $errors[0] ?? '', $this->errors);
    }

    /**
     * Prüfen ob Feld Fehler hat
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Erste Fehlermeldung für Feld
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    // ================================
    // Validierungsregeln
    // ================================

    private function validateRequired(string $field, mixed $value, ?string $param): bool
    {
        if ($value === null)
            return false;
        if (is_string($value) && trim($value) === '')
            return false;
        if (is_array($value) && empty($value))
            return false;
        return true;
    }

    private function validateEmail(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;
        return mb_strlen((string) $value) >= (int) $param;
    }

    private function validateMax(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;
        return mb_strlen((string) $value) <= (int) $param;
    }

    private function validateNumeric(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || is_numeric($value);
    }

    private function validateInteger(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateDecimal(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || preg_match('/^\d+(\.\d{1,2})?$/', $value);
    }

    private function validateAlpha(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || preg_match('/^[\pL\s]+$/u', $value);
    }

    private function validateAlphanumeric(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || preg_match('/^[\pL\pN\s]+$/u', $value);
    }

    private function validateConfirmed(string $field, mixed $value, ?string $param): bool
    {
        $confirmField = $param ?? ($field . '_confirmation');
        return $value === ($this->data[$confirmField] ?? null);
    }

    private function validateUrl(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateDate(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;
        $format = $param ?? 'Y-m-d';
        $date = \DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) === $value;
    }

    private function validateIn(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;
        $allowed = explode(',', $param);
        return in_array($value, $allowed);
    }

    private function validateRegex(string $field, mixed $value, ?string $param): bool
    {
        return empty($value) || preg_match($param, $value);
    }

    private function validatePassword(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;
        // Min 8 Zeichen, 1 Großbuchstabe, 1 Zahl
        return preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $value);
    }

    private function validateUnique(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;

        [$table, $column, $exceptId] = array_pad(explode(',', $param), 3, null);
        $column = $column ?? $field;

        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $params = [$value];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() === 0;
    }

    private function validateExists(string $field, mixed $value, ?string $param): bool
    {
        if (empty($value))
            return true;

        [$table, $column] = array_pad(explode(',', $param), 2, null);
        $column = $column ?? 'id';

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$value]);

        return $stmt->fetchColumn() > 0;
    }
}

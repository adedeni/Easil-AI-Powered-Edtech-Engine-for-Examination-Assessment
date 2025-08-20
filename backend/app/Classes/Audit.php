<?php
class Audit {
    public static function log(int $actorUserId, string $action, ?int $targetUserId = null, array $details = []) : void {
        try {
            $db = DB::getInstance();
            $db->insert('audit_logs', [
                'actor_user_id' => $actorUserId,
                'action' => $action,
                'target_user_id' => $targetUserId,
                'details' => json_encode($details),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            // Best-effort logging; do not break primary flow
        }
    }
}
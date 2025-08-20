<?php
class CourseModel {
    public static function create($data) {
        $db = DB::getInstance();
        return $db->insert('courses', [
            'code' => $data['code'],
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
            'department' => $data['department'],
            'coordinator_user_id' => $data['coordinator_user_id'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
}
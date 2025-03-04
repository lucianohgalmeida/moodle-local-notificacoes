<?php
namespace local_notificacoes\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class settings_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;

        // Categorias de cursos
        $mform->addElement('autocomplete', 'categories', 
            get_string('config_categories', 'local_notificacoes'), 
            $this->get_course_categories()
        );
        $mform->addRule('categories', null, 'required');

        // Tempos de notificação
        $mform->addElement('text', 'student_reminder_hours', 
            get_string('config_student_reminder', 'local_notificacoes'),
            ['type' => 'number', 'min' => 1]
        );
        $mform->setType('student_reminder_hours', PARAM_INT);
        $mform->setDefault('student_reminder_hours', 72);

        $mform->addElement('text', 'teacher_alert_hours', 
            get_string('config_teacher_alert', 'local_notificacoes'),
            ['type' => 'number', 'min' => 1]
        );
        $mform->setType('teacher_alert_hours', PARAM_INT);
        $mform->setDefault('teacher_alert_hours', 24);

        $this->add_action_buttons();
    }

    private function get_course_categories() {
        global $DB;
        return $DB->get_records_menu('course_categories', null, 'name', 'id,name');
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        if ($data['student_reminder_hours'] < 1) {
            $errors['student_reminder_hours'] = get_string('invalid_hours', 'local_notificacoes');
        }
        
        if ($data['teacher_alert_hours'] < 1) {
            $errors['teacher_alert_hours'] = get_string('invalid_hours', 'local_notificacoes');
        }
        
        return $errors;
    }
}
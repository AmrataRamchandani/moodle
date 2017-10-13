<?php 
defined('MOODLE_INTERNAL') || die();

function useripmapping_accessrule_extend_navigation($accessrulenode, $cm ) {
    if (has_capability('mod/quiz:manage', $cm->context)) {
        $url = new moodle_url('/mod/quiz/accessrule/useripmapping/managemappings.php', 
            array('quizid'=>$cm->instance,'courseid'=>$cm->course, 'cmid'=>$cm->id));
        $node = navigation_node::create('Manage mappings',
                     $url,
                     navigation_node::TYPE_SETTING, null, 'quiz_accessrule_useripmapping',
                     new pix_icon('i/item', ''));
      $managenode =  $accessrulenode->add_node($node);
        
//       $importurl=new moodle_url('/mod/quiz/accessrule/useripmapping/importmappings.php',
//           array('quizid'=>$cm->instance,'courseid'=>$cm->course, 'cmid'=>$cm->id));;
          
//       $importnode=navigation_node::create('Import mappings',
//               $importurl,
//               navigation_node::TYPE_SETTING, null, 'quiz_accessrule_useripmapping',
//               new pix_icon('i/item', ''));;
      
//         $managenode->add_node($importnode);
        
//         $editurl=new moodle_url('/mod/quiz/accessrule/useripmapping/editmapping.php',
//             array('quizid'=>$cm->instance,'courseid'=>$cm->course, 'cmid'=>$cm->id));;
            
//         $editnode=navigation_node::create('Edit mappings',
//                 $importurl,
//                 navigation_node::TYPE_SETTING, null, 'quiz_accessrule_useripmapping',
//                 new pix_icon('i/item', ''));;
                
//         $managenode->add_node($editnode);
    }
}

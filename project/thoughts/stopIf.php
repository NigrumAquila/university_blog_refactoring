<?php

do {
  
    if( !process_x() )
      { clean_all_processes();  break; }
    
    /* do a lot of other things */
    
    if( !process_y() )
      { clean_all_processes();  break; }
    
    /* do a lot of other things */
    
    if( !process_z() )
      { clean_all_processes();  break; }
    
    /* do a lot of other things */
    /* SUCCESS */
    
  } while (0);

?>
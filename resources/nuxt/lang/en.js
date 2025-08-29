export default {
  gl: {
    image: "Image",
    next: "Next",
    back: "Back",
    done: "Done",
    cancel: "Cancel",
    add: "Add",
    lessons: ["Lesson", "Lessons", "Lessons"],
    lesson: "Lesson",
    time: {
      pm: "PM",
      am: "AM",
    },
    week: [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ],
    days: ["Day", "Days", "Days"],
    or: "Or",
    form: {
      email: "Email",
    },
    msg: {
      save: "Saved successfully",
      error: {
        sendData:
          "Failed to send data to the server, please check your internet connection and try again",
        getData:
          "Failed to fetch data from the server, please check your internet connection and try again",
        share: "Your browser does not support sharing or copying",
        cache: {
          support: "Your browser does not support caching!",
          found:
            "No cache found for this page, please check your internet connection and try again",
          put:
            "Failed to save cache for this page, please check your internet connection and try again",
          open:
            "Failed to open cache for this page, please check your internet connection and try again",
          match:
            "Failed to match cache for this page, please check your internet connection and try again",
        },
        process:
          "An error occurred during processing, please check your internet connection and try again",
      },
      share: {
        linkCopied: "Link copied",
      },
    },
    loading: {
      more: "Load more",
      wait: "Please wait...",
    },
    yes: "Yes",
    no: "No",
    needInternet: "Internet connection required!",
    and : "and"
  },
  pages: {
    prefs: {
      title: "Preferences",
      boxs: {
        lang: "Change Language",
        dark: "Dark Mode",
      },
      autoLang: "Auto (Based on Browser Language)",
    },
    start: {
      title: "Let's Start",
      error: {
        limit: `You cannot create a {type} because the limit has been exceeded ({limit}) to create new list, please delete a {type} you don't need.`,
      },
      elements: {
        title: "Choose Task Type",
        catg: {
          course: "Course",
          board: "Task Board",
        },
      },
      typeStart: {
        course: {
          nameCourse: {
            title: "Course Name",
            input: "Programming",
            error: "Please enter course name (maximum 20 characters)",
          },
          lessons: {
            title: "Lessons",
            input: "Lesson name here...",
            error: "No field can be left empty (maximum 50 characters)",
            comment: [
              "You can add lessons by clicking here",
              "Or paste the text in any box or through keyboard buttons",
            ],
            popup: {
              title: "Add Lessons",
              numberLes: {
                label: "Number of Lessons",
                comment: "These number of lessons will be added to the lessons box",
              },
              customWord: {
                label: "Customize Word",
                comment:
                  'Will count the lessons according to this word, for example "Lesson (1)" Customize this word to another word',
              },
              errors: {
                numberLes: [
                  "Please check the number of lessons, maximum",
                  "You can add",
                ],
                limit: `The limit of {limit} lessons has been reached, you cannot add more lessons`,
                word: "Cannot exceed 40 characters",
              },
            },
          },
          done: {
            title: "Completion",
            days: {
              label: "In how many days do you want to complete it?",
              error: "Please check the number of days, maximum is",
            },
            weekend: {
              added: "Compensate Weekends with additional days",
              label: "Weekend?",
              error: {
                full: "The whole week cannot be a Weekend!",
                short: `You want to learn in {days} days, and {weekend} Weekends, when will you learn?`,
              },
            },
            result: {
              text: [
                "Every day",
                "God willing after",
                "Complete it",
                "Or vacation",
                "Few hours",
              ],
            },
          },
          summary: {
            title: "Summary",
            text: [
              "I will start a course",
              "It currently has",
              " And I learn daily,",
              "Except on day",
              ", And after",
              "Corresponding to the date",
              "I will complete this course, God willing",
            ],
            and: "And",
          },
          createMsg: "Course created successfully!",
        },
        board: {
          nameBoard: {
            title: "Task Board Name",
            input: "My new project",
            error: "Please enter task board name (maximum 20 characters)",
          },
          createMsg: "Task board created successfully!",
        },
        comp: {
          image: {
            size: "2 MB maximum",
            unselect: "No image selected",
            error: {
              big: "Image size is large, maximum width is 5000x5000",
              small: "Image size is small, minimum width is 50X92",
              type: "Please check the image size or type",
            },
          },
        },
      },
    },
    index: {
      firstSect: {
        title: "LET'S GET SOMETHING DONE...START NOW",
        smallWord: "Go! 🚀",
        comment:
          "It's simple. 1. Add the course lessons you want to learn. 2. Set a deadline for completing the course. website for planning, completing and evaluating course completion",
        buttons: {
          signup: "Sign Up Now!",
          example: "Example",
          dashboard : "Dashboard",
        },
      },
      features: {
        noAds: {
          title: "AD-FREE",
          comment: "Enjoy without annoying ads, and focus on your courses.",
        },
        feedbacks: {
          title: "EVALUATE PROGRESS",
          comment:
            "It evaluates the achievement rate from 5 different evaluations, and shows your achievement rate in the course.",
          eval: ["Excellent", "Very Good", "Good", "Bad", "Very Bad"],
        },
        free: {
          title: "FREE",
          comment: "All of these features and more are currently completely free.",
        },
        multi: {
          title: "MULTIPLE",
          comment:
            "You can create more than one course and work on them at the same time without any problems.",
          image: [
            {
              title: "Graphic Design",
              date: "(8 days)",
            },
            {
              title: "PHP Course",
              date: "(20 days)",
            },
            {
              title: "Emotional Intelligence",
              date: "(3 days)",
            },
          ],
        },
      },
      otherFeat: {
        title: "OTHER FEATURES",
        elements: [
          "Choose vacation days",
          "Multilingual",
          "Add lessons to the course",
          "PWA feature",
          "Tasks or courses",
          "Gives you today's lessons",
          "Share your courses",
          "Dark mode",
          "Write notes",
        ],
      },
      register: {
        title: "Sign Up Now",
        comment:
          "Start learning the skills you've always wanted to master, or set tasks you want to accomplish... Let's go.",
      },
    },
    dashboard: {
      active: {
        course: {
          title: "ACTIVE COURSES",
          notFound: {
            title: "You have no active courses!",
            comment: ["Do you want to start a", "new course"],
          },
        },
        board: {
          title: "ACTIVE TASK BOARDS",
          sub: "Sub Board",
          task: "Task",
          taskLeft: "Task Left",
          notFound: {
            title: "You have no task boards!",
            comment: ["Do you want to start a", "new task board"],
          },
        },
        daysLeft: {
          late: "More than -9999 days late",
          early: "More than +9999 days left",
        },
      },
      boards: {
        title: "TASK BOARDS",
        sub: "Sub Board",
        task: "Task",
        taskLeft: "Task Left",
        notFound: {
          title: "You have no task boards!",
          comment: ["Do you want to start a", "new task board"],
        },
        filter: {
          title: "Filter",
          sort: {
            title: "Sort By",
            oldToNew: "Oldest to Newest",
            newToOld: "Newest to Oldest",
            lastActive: "Last Active",
            rate: "Progress Rate",
          },
          type: {
            title: "Type",
            done: "Done",
            notDone: "Not Done",
          },
        },
        daysLeft: {
          late: "More than -9999 days late",
          early: "More than +9999 days left",
        },
        list: {
          public: "Set to Public",
          private: "Set to Private",
          share: "Share",
          remove: "Delete",
        },
        msg: {
          status: {
            sure: {
              text: [
                "Are you sure you want to change the privacy of the task board",
                "to"
              ],
              private:
                "(Private Board), This board will become private to you only and only you can see it.",
              public:
                "(Public Board), This board will appear in your account and anyone with the link can view and copy your board.",
            },
            set: {
              text: "Privacy status has been changed to",
              private: "Private Task Board",
              public: "Public Task Board",
            },
          },
          remove: {
            sure: ["Are you sure you want to delete the task board", "?"],
            set: "Task board deleted successfully",
          },
        },
      },
      course: {
        title: "COURSES",
        notFound: {
          title: "You have no active courses!",
          comment: ["Do you want to start a", "new course"],
        },
        filter: {
          title: "Filter",
          sort: {
            title: "Sort By",
            oldToNew: "Oldest to Newest",
            newToOld: "Newest to Oldest",
            lastActive: "Last Active",
            rate: "Progress Rate",
          },
          type: {
            title: "Type",
            done: "Done",
            notDone: "Not Done",
          },
        },
        daysLeft: {
          late: "More than -9999 days late",
          early: "More than +9999 days left",
        },
        list: {
          public: "Set to Public",
          private: "Set to Private",
          share: "Share",
          remove: "Delete",
        },
        msg: {
          status: {
            sure: {
              text: [
                "Are you sure you want to change the privacy of the course",
                "to"
              ],
              private:
                "(Private Course), This course will become private to you only and only you can see it.",
              public:
                "(Public Course), This course will appear in your account and anyone with the link can view and copy your board.",
            },
            set: {
              text: "Privacy status has been changed to",
              private: "Private Course",
              public: "Public Course",
            },
            remove: {
              sure: ["Are you sure you want to delete the course", "?"],
              set: "Course deleted successfully",
            },
          },
        },
      },
    },

    course: {
      all: {
        title: "ALL LESSONS",
        btn: "Add New Lessons",
        note: "Notebook",
        pop: {
          lesson: {
            add: {
              title: "Add Lessons",
              numberLes: {
                label: "Number of Lessons",
                comment: "This number of lessons will be added to the added lessons box",
              },
              customWord: {
                label: "Customize Word",
                comment:
                  'It will arrange the lessons according to this word, for example "Lesson (1)". Assign this word to another word',
              },
              btn: "Add Lessons",
              addedLes: {
                title: "Added Lessons",
                comment:
                  "You can add lessons by clicking the button above, pasting text into any box, or using keyboard buttons",
                placeholder: "Lesson Name...",
              },
              errors: {
                numberLes: [
                  "Please check the number of lessons, maximum",
                  "You can add",
                ],
                limit: `You have reached the limit {limit}, you cannot add more lessons`,
                word: "Cannot exceed 40 characters",
                addedLes: "No field can be left empty (maximum 50 characters)",
              },
            },
            note: {
              doneIn: "Done In",
              options: {
                rename: "Rename",
                move: "Move",
                copy: "Copy",
                remove: "Delete",
              },
            },
            move: {
              title: "Move",
              before: "Before",
              after: "After",
              oneLesson: "There is only one lesson",
               error : "A completed lesson cannot be moved to a future date. The lesson has been completed."
            },
          },
        },
        msg: {
          add: "Lessons added successfully",
          wait: "Please wait, the last edit data has not been sent",
          remove: {
            sure: ["Are you sure you want to delete the lesson", "lesson", "?"],
            done: "Lesson deleted successfully",
          },
          move: {
            sure: ["Are you sure you want to move the lesson", "?"],
            done: "Lesson moved successfully",
          },
          copy: {
            sure: ["Are you sure you want to copy the lesson", "?"],
            done: "Lesson copied successfully",
          },
          // !!!
          rename: {
            error:
              "The size of the lesson is too large, maximum 50 characters. Do you want to copy the previous text?",
            done: "Lesson name changed successfully",
          },
          note: `The last edit has not been saved, the text area is large, it cannot exceed {limit} bytes, the space is now {size} bytes`,
        },
        cache: {
          save:
            "Failed to save new data for today's lessons page, please connect to the internet and try again",
          opne:
            "Failed to open the cache for today's lessons page, please connect to the internet and try again",
          match:
            "Failed to find the cache for today's lessons page, please connect to the internet and try again",
        },
        copyWord: " - Copy",
      },
      settings: {
        title: "COURSE SETTINGS",
        nameCourse: {
          title: "Edit Course Name",
          error: "Please write the course name (maximum 20 characters)",
        },

        notifs: {
          title : "Receive course notifications",
          error : "You cannot open notifications, due to account settings. To activate notifications, modify the email sending settings."
        },
        deleteCourse: {
          title: "Delete Course",
          sure: "Are you sure you want to delete the course?",
        },
        done_days: {
          limit: `The limit for setting days from the start of creating the course is `,
          title : {
            inSpace : "Modify days remaining to expiration",
            outSpace : "Modify remaining working days"
          },
          description : "These are the remaining days without Weekends"
        },
        weekend: {
          title: "Edit Weekend Days",
          error : "There is no day left to learn, reduce the days off to leave room for learning or increase the days left to finish",
          limit: "You cannot set more than 6 weekend days"
        },
        redist: {
          title: "Rearrange Delay",
          description:
            "This feature allows you to reset overdue lessons, as if you started today with these lessons...All lessons will be rearranged.",
          sure: "Are you sure you want to reorder all the lessons? The progress will be reset.",
          done: "Overdue lessons have been successfully rearranged!"
        },
        image: {
          title: "Change Image",
          info: "Maximum 2 MB, size 230x125 format",
          done: "Image changed successfully!!",
          error: {
            cant: "Failed to change the image, please try again",
            type: "This file cannot be selected due to the format or size",
            big: "The image size is too large, the maximum width is 5000x5000",
            small: "The image size is too small, the minimum width is 50X92"
          }
        },
        private: {
          sure: "Are you sure you want to change the course privacy to",
          privateCourse: "Private Course",
          publicCourse: "Public Course",
          private: "Private",
          public: "Public",
          privateComment:
            ", This course will become private to you only and only you can see it.",
          publicComment:
            ", This course will appear in your account and anyone with the link can view and copy your board.",
        },
      },
      stats: {
        title: "COURSE STATISTICS",
      },
      today: {
        title: "TODAY'S LESSONS",
        doneBox: {
          title: "Today's Lessons Done",
          btn: "Add New Lesson for Today",
        },
        noLessonsBox: {
          title: "No Lessons Today",
          comment: ["You deserve a little break, or", "Add a new lesson for today"],
        },
        newLes: " New Lesson",
        lateLes: "Late",
        note: "Notebook",
        msg: {
          wait: "Please wait, the last edit data has not been sent",
          note : `The last edit has not been saved, the text area is large, it cannot exceed {limit} bytes, the space is now {size} bytes`,
        },
        cache: {
          save:
            "Failed to save new data for the All Lessons page, please connect to the internet and try again",
          opne:
            "Failed to open the cache for the All Lessons page, please connect to the internet and try again",
          match:
            "Failed to find the cache for the All Lessons page, please connect to the internet and try again",
        },
      },
      finish: {
        title: "THE END",
        box: {
          text: [
            "Alhamdulillah, I completed the course",
            "Well done! .. We say goodbye to you here .. You made a great effort .. We thank you for that and good luck, God willing ..",
          ],
          stats: {
            start: "Course Start",
            end: "Course End",
            exp: "Expected Date",
            earlyWith: "Early by",
            left: "Days Taken",
            early: "Early",
            late: "Late",
            lessons: "Lessons",
          },
          share: {
            title: "Share",
            navigator: "Share Course",
            whatsapp: "WhatsApp",
            twitter: "Twitter",
            telegram: "Telegram",
            facebook: "Facebook (Website Link Only)",
            more: "More",
            copy: "Copy",
            error: "Sorry, it seems this feature is not available on this browser",
          },
          shareText: `Alhamdulillah, I completed the course
    
            ~~~~ %%% ~~~~
            Well done! .. You made a great effort ..
    
            Statistics :
    
            Course Start : %%%
            Course End : %%%
            Days Taken : %%%
            Lessons : %%%
    
            Powered by %%% one of %%% products
            %%%
            `,
          copy: "Text copied successfully",
        },
      },
      cant: "Sorry, you cannot edit the lesson right now, please connect to the internet and try again.",
    },
    profile: {
      courses: "COURSES",
      boards: {
        title: "TASK BOARDS",
        sub: "Sub Board",
        task: "Task",
        taskLeft: "Tasks Left",
      },
      private: {
        course: {
          title: "PRIVATE COURSES",
          comment: "No public courses available",
        },
        boards: {
          title: "PRIVATE TASK BOARDS",
          comment: "No public task boards available",
        },
      },
      error: "This user does not exist",
    },
    read: {
      title: "READ",
      found: "Sorry, there is nothing currently",
    },
    install: {
      title: "INSTALL",
      pc: {
        title: "PC",
        text: [
          "In the search bar, there is an icon on the left (Install)",
          "Click on it",
          "* The installation icon is not the same as the shape of this icon, just for explanation",
        ],
      },
      android: {
        title: "Android",
        text: [
          "Next to the search bar, there is an icon",
          "(three dots), click on it, a list of options will appear, select (Install app), then click install",
          "* The installation icon is not the same as the shape of this icon, just for explanation",
        ],
      },
      iphone: {
        title: "iPhone",
        text: [
          "In the Safari browser, there is an icon at the bottom (Share), click on it, several options will appear, select from them (Add to Home Screen)",
          "In the upper left corner you will find an (Add) button, click on it",
        ],
      },
    },
    update: {
      title: "UPDATES",
      current: "Current Update",
      added: "Added",
      removed: "Removed",
      found: "Sorry, there is nothing currently",
    },
    about: {
      title: "ABOUT",
      description:
        `"Welcome, 'Nashit' is a site for planning and tracking course learning and progress, and also provides the feature of creating task boards."`,
      share: "Follow Juzr on social media",
      report:
        "To report problems or suggestions, please contact us or message us on social media.",
    },
    board: {
      index: {
        options: {
          details: "Details",
          rename: "Rename",
          copy: "Copy",
          delete: "Delete",
          move: "Move",
        },
        tasksPlaceholder: "The task is...",
        pops: {
          todoList: {
            details: {
              title: "Details",
              name: "Todo List Name",
              create: "Todo List created on",
              lastUpdate: "Todo List last updated on",
            },
            create: {
              title: "Create Todo List",
              name: "Todo List Name",
              error: "Name must be between 1-20 characters",
            },
            rename: {
              title: "Rename",
              name: "Todo List Name",
              error: "Name must be between 1-20 characters",
            },
            copy: {
              title: "Copy Todo List",
              withTasks: "With Tasks",
              withoutTasks: "Without Tasks",
            },
          },
          task: {
            details: {
              title: "Details",
              name: "Task Name",
              done: "Task completed on",
              create: "Task created on",
              lastUpdate: "Task last updated on",
            },
            move: {
              title: "Move",
              to: ["Move Task", "to"],
              noTodoLists: "There are no other todo lists you can move the task to.",
            },
          },
        },
        msg: {
          wait: "Please wait, the last edit data has not been sent",
          todoList: {
            delete: {
              sure: "Are you sure you want to delete the todo list?",
              one: "at least one todo list.",
              done: "Todo list deleted successfully",
              error:
                "Sorry, you cannot delete the todo list right now, please connect to the internet and try again.",
            },
            copy: {
              done: "Todo list copied successfully",
              error:
                "Sorry, you cannot copy the todo list right now, please connect to the internet and try again.",
            },
            create: {
              cant:
                "Sorry, you cannot create a todo list right now, please connect to the internet and try again.",
              limit: `Cannot create new list, todo list limit is`,
            },
            cant:
              "Sorry, you cannot edit the todo list right now, please connect to the internet and try again.",
          },
          task: {
            create: {
              limitLetters: "Task length is too long, maximum 50 characters",
              limitLength: `Task limit exceeded`,
              error:
                "Sorry, you cannot create a task right now, please connect to the internet and try again.",
            },
            change:
              "Sorry, the new task location has not been saved yet, the task will return to its original location when the page is refreshed, please connect to the internet and try again.",
            copy: {
              done: "Task copied successfully",
              error:
                "Sorry, you cannot copy the task right now, please connect to the internet and try again.",
            },
            rename: {
              error:
                "Task length is too long, maximum 50 characters. Do you want to copy the previous text?",
              done: "Task name changed successfully",
            },
            delete: {
              sure: "Are you sure you want to delete the task?",
              done: "Task deleted successfully",
              error:
                "Sorry, you cannot delete the task right now, please connect to the internet and try again.",
            },
            move: {
              sure: ["Are you sure you want to move the task", "to a todo list"],
              error:
                "Sorry, you cannot move the task right now, please connect to the internet and try again.",
            },
            note: `The last edit has not been saved, the text area is large, it cannot exceed {limit} bytes, the space is now {size} bytes`,
            cant:
              "Sorry, you cannot edit the task right now, please connect to the internet and try again.",
          },
        },
        copied: " - Copy",
      },
      settings: {
        title: "BOARD SETTINGS",
        nameBoard: {
          title: "Edit Board Name",
          error: "Please write the board name (maximum 20 characters)",
        },

        notifs: {
          title : "Receive notifications for this board",
          error : "You cannot open notifications, due to account settings. To activate notifications, modify the email sending settings."
        },
        deleteBoard: {
          title: "Delete Board",
          sure: "Are you sure you want to delete the entire board?",
        },
        image: {
          title: "Change Image",
          info: "Maximum 2 MB, size 230x100 format",
          done: "Image changed successfully!!",
          error: {
            cant: "Failed to change the image, please try again",
            type: "This file cannot be selected due to the format or size",
            big: "The image size is too large, the maximum width is 5000x5000",
            small: "The image size is too small, the minimum width is 50X115",
          },
        },
        private: {
          sure: "Are you sure you want to change the board privacy to",
          privateBoard: "Private Board",
          publicBoard: "Public Board",
          private: "Private",
          public: "Public",
          privateComment:
            ", This board will become private to you only and only you will be able to see it",
          publicComment:
            ", This board will appear in your account and anyone with the link can view and copy your board",
        },
      },
      stats: {
        title: "BOARD STATISTICS",
      },
    },
    registration: {
      text: "Welcome {name}, we hope you have a wonderful time on this island!! This project took a long time, we appreciate you being here today, have a great day.",
      go: "Let's go",
    },
  },
  components: {
    pageItems: {
      rate: {
        eval: [
          "Excellent",
          "Very Good",
          "Good",
          "Bad",
          "Very Bad",
          "Ultimate",
        ],
        evalRatio: {
          early: "Early",
          late: "Late",
        },
      },
    },
    footerApp: {
      anas : ["Developed by", "anas.repo"],
      about: {
        text: "It's simple. 1. Add the course lessons you want to learn. 2. Set a deadline for completing the course. website for planning, completing and evaluating course completion",
        from: "One of the products",
      },
      pages: {
        title: "PAGES",
        signup: "Sign Up",
        login: "Login",
        dashboard: "Dashboard",
        createAccount: "Create Account",
        createCourse: "Create New Course",
      },
      imp: {
        title: "IMPORTANT",
        pp: "Privacy Policy",
        tc: "Terms and Conditions",
        credits : "Sites Credits"
      },
      plat: {
        title: "Juzr",
        website: "Website",
        apps: "Products",
      },
    },
    headerApp: {
      buttons: {
        login: "Login",
        signup: "Sign Up",
      },
    },
    navUser: {
      logout:
        "There are unsaved changes, logging out will not sync data, please connect to the internet and then log out again. Do you want to log out anyway?",
      list: {
        profile: "Profile",
        dashboard: "Dashboard",
        myAccount: "My Account",
        logout: "Logout",
      },
    },
    noteBook: {
      placeholder: "Write something...",
      labels: {
        normal: "Normal",
        title: "Title",
        big: "Large",
        medium: "Medium",
        small: "Small",
      },
      cant:
        "Sorry, you cannot edit the data right now, please connect to the internet and try again.",
    },
    pop: {
      imageCrop: {
        customize: "Customize Image",
        select: "Select Image",
        alert: "Alert: The image will be uploaded to our servers and then cropped.",
      },
    },
  },
  layouts: {
    page: {
      buttons: {
        login: "Login",
        register: "Register",
      },
      course: {
        link: {
          finish: "Course Completion",
          stats: "Statistics",
          today: "Today's Lessons",
          all: "All Lessons",
          settings: "Course Settings",
        },
        buttons: {
          copy: "Copy Course",
        },
        copy: {
          title: "Copy Course",
          done: {
            title: "Completion",
            days: {
              label: "In how many days do you want to complete it?",
              error: "Please check the number of days, the maximum duration is",
            },
            weekend: {
              added: "Compensate Weekends with extra days",
              label: "Weekends?",
              error: "The whole week cannot be a Weekend!",
            },
            result: {
              text: [
                "Every day",
                "God willing after",
                "Complete it",
                "Or a vacation",
                "A few hours",
              ],
            },
            error: `You want to learn in {day} days, and there are {day} Weekends. When will you learn?`,
          },
        },
      },
      board: {
        link: {
          stats: "Statistics",
          board: "Board",
          settings: "Board Settings",
        },
        buttons: {
          copy: "Copy Entire Board",
          create: "Create Todo List",
        },
        pops: {
          copy: {
            title: "Copy Board",
            button: "Copy",
            withTasks: "With Tasks",
            withoutTasks: "Without Tasks",
          },
        },
        msg: {
          copy: {
            sure: ["Are you sure you want to copy a board", "?"],
          },
          showCreateTodo: "To create a todo list, you must be on the 'Board' page",
        },
      },
      global: {
        title: "General",
        link: {
          dashboard: "Dashboard",
          main: "Home",
          prefs: "Preferences",
          install: "Install",
          report: "Report",
          about: "About",
          read: "Read",
        },
        more: "More",
        close: "Close",
        buttons: {
          share: "Share",
        },
      },
      noInternet:
        "Please connect to the internet soon to sync data, if the session ends the data will be deleted",
    },
    dashboard: {
      title: "Dashboard",
      list: {
        active: "Active",
        courses: "Courses",
        boards: "Boards",
      },
      btn: "New",
    },
    error: {
      text: "Sorry, this page does not exist. It may have been deleted, or please check the link.",
      main: "Go to Home",
    },
  },
  plugins: {
    logout: {
      out: "Logging out...",
      deletePrivateData: "Private data deleted successfully.",
      errorDeletePrivateData:
        "An error occurred while deleting private data. The page will reload in 10 seconds.",
      error : "Unable to log out, please try again"
    },
    sync: {
      syncData: "Synchronizing data...",
      done: "Data synchronization successful.",
      error:
        "An error occurred while synchronizing stored data. The page will reload in 10 seconds.",
    },
  },
  config: {
    title: "Nashit {'|'} Achievement Island",
    names: {
      nashit: "Nashit {'|'} Achievement Island",
      juzr: "Juzr {'|'} جُزر",
    },
  },
  utils: {
    $api: {
      many: "Too many requests, please try again in a minute."
    }
  }
};
